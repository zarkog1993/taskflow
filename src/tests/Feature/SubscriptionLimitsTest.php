<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Player;
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

    public function test_cannot_create_player_when_club_limit_is_reached_across_teams(): void
    {
        $club = Club::create(['name' => 'FK Players']);
        $admin = User::factory()->create(['club_id' => $club->id]);
        $admin->roles()->sync([Role::where('slug', 'club-admin')->first()->id]);
        Subscription::create([
            'user_id' => $admin->id,
            'club_id' => $club->id,
            'plan_type' => 'basic',
            'status' => 'active',
            'max_teams' => 2,
            'max_players' => 1,
            'features' => ['players', 'teams'],
            'ends_at' => now()->addMonth(),
        ]);

        $firstTeam = Team::create([
            'name' => 'First Team',
            'age_group' => 'u19',
            'club_id' => $club->id,
        ]);
        $secondTeam = Team::create([
            'name' => 'Second Team',
            'age_group' => 'senior',
            'club_id' => $club->id,
        ]);
        Player::create([
            'name' => 'Existing Player',
            'primary_position' => 'CM',
            'team_id' => $firstTeam->id,
            'club_id' => $club->id,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/players', [
                'name' => 'Over Limit Player',
                'primary_position' => 'ST',
                'team_id' => $secondTeam->id,
            ])
            ->assertForbidden()
            ->assertJsonPath('message', 'The player limit for this subscription has been reached.');

        $this->assertDatabaseMissing('players', ['name' => 'Over Limit Player']);
    }

    public function test_can_create_player_below_club_limit(): void
    {
        $club = Club::create(['name' => 'FK Within Limit']);
        $admin = User::factory()->create(['club_id' => $club->id]);
        $admin->roles()->sync([Role::where('slug', 'club-admin')->first()->id]);
        Subscription::create([
            'user_id' => $admin->id,
            'club_id' => $club->id,
            'plan_type' => 'basic',
            'status' => 'active',
            'max_teams' => 1,
            'max_players' => 1,
            'features' => ['players', 'teams'],
            'ends_at' => now()->addMonth(),
        ]);
        $team = Team::create([
            'name' => 'Within Limit Team',
            'age_group' => 'u19',
            'club_id' => $club->id,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/players', [
                'name' => 'First Player',
                'primary_position' => 'ST',
                'team_id' => $team->id,
            ])
            ->assertCreated();

        $this->assertDatabaseHas('players', [
            'name' => 'First Player',
            'club_id' => $club->id,
        ]);
    }

    public function test_roster_player_limit_counts_roster_records_not_player_login_accounts(): void
    {
        $club = Club::create(['name' => 'FK Separate Player Limits']);
        $admin = User::factory()->create(['club_id' => $club->id]);
        $admin->roles()->sync([Role::where('slug', 'club-admin')->first()->id]);
        Subscription::create([
            'user_id' => $admin->id,
            'club_id' => $club->id,
            'plan_type' => 'basic',
            'status' => 'active',
            'max_teams' => 2,
            'max_players' => 1,
            'features' => ['players', 'teams'],
            'ends_at' => now()->addMonth(),
        ]);
        $team = Team::create([
            'name' => 'Separate Limits Team',
            'age_group' => 'u19',
            'club_id' => $club->id,
        ]);
        $playerRole = Role::where('slug', 'player')->firstOrFail();

        foreach (range(1, 3) as $number) {
            $playerAccount = User::factory()->create(['club_id' => $club->id]);
            $playerAccount->roles()->attach($playerRole);
        }

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/players', [
                'name' => 'First Roster Player',
                'primary_position' => 'ST',
                'team_id' => $team->id,
            ])
            ->assertCreated();

        $this->postJson('/api/players', [
            'name' => 'Over Limit Roster Player',
            'primary_position' => 'CM',
            'team_id' => $team->id,
        ])
            ->assertForbidden()
            ->assertJsonPath('message', 'The player limit for this subscription has been reached.');

        $this->assertDatabaseCount('players', 1);
    }
}
