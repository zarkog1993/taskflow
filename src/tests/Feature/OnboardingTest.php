<?php

namespace Tests\Feature;

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

    public function test_user_can_complete_onboarding_and_create_club_with_subscription(): void
    {
        $user = User::factory()->create();

        $payload = [
            'club_name' => 'FK Srem Vrdnik',
            'plan_type' => 'pro',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/onboarding/complete', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('club.name', 'FK Srem Vrdnik');

        // Provera baze
        $this->assertDatabaseHas('clubs', [
            'name' => 'FK Srem Vrdnik',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'club_id' => $response->json('club.id'),
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'plan_type' => 'pro',
            'max_teams' => 5,
            'max_players' => 150,
            'status' => 'active',
        ]);
    }
}