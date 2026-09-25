<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['slug' => 'club-admin'], ['name' => 'Club Admin']);
    }

    public function test_user_can_select_pending_subscription_for_registered_club(): void
    {
        $club = Club::create([
            'name' => 'FK Srem Vrdnik',
            'status' => 'pending',
        ]);
        $user = User::factory()->create(['club_id' => $club->id]);

        $payload = [
            'club_name' => 'FK Srem Vrdnik',
            'plan_type' => 'pro',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/onboarding/complete', $payload);

        $response->assertStatus(202)
            ->assertJsonPath('club.name', 'FK Srem Vrdnik');

        $this->assertDatabaseHas('clubs', [
            'id' => $club->id,
            'name' => 'FK Srem Vrdnik',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'club_id' => $club->id,
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_type' => 'pro',
            'max_teams' => 5,
            'max_players' => 150,
            'status' => 'pending',
        ]);
    }
}