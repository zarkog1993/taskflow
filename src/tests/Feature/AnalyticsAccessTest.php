<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_premium_subscriber_can_access_analytics(): void
    {
        $user = $this->createSubscribedUser(['advanced_stats']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/analytics')
            ->assertOk()
            ->assertJsonStructure([
                'teams',
                'players',
                'summary',
            ]);
    }

    public function test_subscriber_without_advanced_statistics_cannot_access_analytics(): void
    {
        $user = $this->createSubscribedUser(['players', 'teams', 'matches']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/analytics')
            ->assertForbidden()
            ->assertJsonPath('code', 'feature_not_included')
            ->assertJsonPath('feature', 'advanced_stats');
    }

    private function createSubscribedUser(array $features): User
    {
        $club = Club::create([
            'name' => fake()->company(),
            'status' => 'active',
        ]);
        $user = User::factory()->create(['club_id' => $club->id]);

        Subscription::create([
            'user_id' => $user->id,
            'club_id' => $club->id,
            'plan_type' => in_array('advanced_stats', $features, true) ? 'premium' : 'standard',
            'status' => 'active',
            'max_teams' => 5,
            'max_players' => 150,
            'features' => $features,
            'ends_at' => now()->addMonth(),
        ]);

        return $user;
    }
}
