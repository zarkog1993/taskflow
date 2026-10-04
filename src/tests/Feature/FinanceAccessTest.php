<?php

namespace Tests\Feature;

use App\Models\Academy;
use App\Models\Club;
use App\Models\Player;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_finance_routes_require_the_teams_subscription_feature(): void
    {
        [$user, $team] = $this->createUserWithSubscription(['players'], 'active');

        $requests = [
            fn () => $this->getJson('/api/finances/overview'),
            fn () => $this->getJson("/api/teams/{$team->id}/payments"),
            fn () => $this->postJson('/api/payments', []),
            fn () => $this->postJson('/api/payments/bulk', []),
        ];

        foreach ($requests as $request) {
            $this->actingAs($user, 'sanctum');
            $request()
                ->assertForbidden()
                ->assertJsonPath('code', 'feature_not_included')
                ->assertJsonPath('feature', 'teams');
        }

        $this->assertDatabaseCount('player_payments', 0);
    }

    public function test_finance_routes_remain_available_to_active_subscribers_with_teams_feature(): void
    {
        [$user, $team, $player] = $this->createUserWithSubscription(['teams', 'players'], 'active');

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/finances/overview')
            ->assertOk();

        $this->getJson("/api/teams/{$team->id}/payments")
            ->assertOk();

        $this->postJson('/api/payments', [
            'team_id' => $team->id,
            'player_id' => $player->id,
            'type' => 'membership',
            'period' => now()->format('Y-m'),
            'amount' => 50,
            'status' => 'paid',
        ])->assertOk();

        $this->postJson('/api/payments/bulk', [
            'type' => 'membership',
            'period' => now()->addMonth()->format('Y-m'),
            'items' => [[
                'team_id' => $team->id,
                'player_id' => $player->id,
                'amount' => 50,
                'status' => 'pending',
            ]],
        ])->assertOk();

        $this->assertDatabaseCount('player_payments', 2);
    }

    public function test_expired_subscription_cannot_access_finances_even_with_teams_feature(): void
    {
        [$user] = $this->createUserWithSubscription(['teams'], 'active', true);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/finances/overview')
            ->assertForbidden()
            ->assertJsonPath('code', 'feature_not_included')
            ->assertJsonPath('feature', 'teams');
    }

    private function createUserWithSubscription(array $features, string $status, bool $expired = false): array
    {
        $club = Club::create(['name' => 'Finance Club', 'status' => 'active']);
        $user = User::factory()->create(['club_id' => $club->id]);

        Subscription::create([
            'user_id' => $user->id,
            'club_id' => $club->id,
            'plan_type' => 'basic',
            'status' => $status,
            'max_teams' => 5,
            'max_players' => 150,
            'features' => $features,
            'ends_at' => $expired ? now()->subDay() : now()->addMonth(),
        ]);

        $academy = Academy::create([
            'name' => 'Finance Academy',
            'slug' => 'finance-academy-' . $club->id,
        ]);
        $team = Team::create([
            'academy_id' => $academy->id,
            'club_id' => $club->id,
            'name' => 'Finance Team',
            'age_group' => 'u19',
        ]);
        $player = Player::create([
            'team_id' => $team->id,
            'club_id' => $club->id,
            'name' => 'Finance Player',
            'primary_position' => 'ST',
        ]);

        return [$user, $team, $player];
    }
}
