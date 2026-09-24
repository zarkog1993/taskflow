<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionLimitsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['slug' => 'club-admin'], ['name' => 'Club Admin']);
        Role::firstOrCreate(['slug' => 'player'], ['name' => 'Player']);
    }

    public function test_cannot_create_team_if_limit_is_reached(): void
    {
        // 1. Kreiranje kluba
        $club = Club::create(['name' => 'FK Srem Vrdnik']);

        // 2. Kreiranje admina kluba
        $admin = User::factory()->create(['club_id' => $club->id]);
        $admin->roles()->sync([Role::where('slug', 'club-admin')->first()->id]);

        // 3. Pretplata na Basic paket (max 1 tim)
        Subscription::create([
            'user_id'     => $admin->id,
            'plan_type'   => 'basic',
            'status'      => 'active',
            'max_teams'   => 1,
            'max_players' => 25,
        ]);

        // 4. Kreiramo prvi tim direktno u bazi
        Team::create([
            'name'      => 'Prvi Tim',
            'age_group' => 'senior', // Proveri po tvojoj validaciji (npr. senior / u19 / u17)
            'club_id'   => $club->id,
        ]);

        // 5. Pokušaj kreiranja drugog tima preko API-ja sa validnim age_group
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/teams', [
                'name'      => 'Drugi Tim',
                'age_group' => 'u19', // Validna vrednost koja prolazi validaciju
            ]);

        // Očekujemo 403 Forbidden jer validacija prolazi, ali Policy blokira zbog limita
        $response->assertStatus(403);
    }
}