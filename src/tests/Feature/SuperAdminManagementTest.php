<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_change_and_disable_a_subscription(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $club = Club::create(['name' => 'Managed Club', 'status' => 'active']);
        $owner = User::factory()->create(['club_id' => $club->id]);
        $basic = $this->createPlan('basic', 1, 25);
        $premium = $this->createPlan('premium', 10, 500);
        $subscription = Subscription::create([
            'user_id' => $owner->id,
            'club_id' => $club->id,
            'subscription_plan_id' => $basic->id,
            'plan_type' => $basic->slug,
            'status' => 'active',
            'max_teams' => $basic->max_teams,
            'max_players' => $basic->max_players,
            'features' => $basic->features,
            'price' => $basic->price,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/super-admin/subscriptions/{$subscription->id}/plan", [
                'plan_id' => $premium->id,
            ])
            ->assertOk()
            ->assertJsonPath('subscription.plan_type', 'premium');

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'subscription_plan_id' => $premium->id,
            'max_teams' => 10,
            'max_players' => 500,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/super-admin/subscriptions/{$subscription->id}/cancel")
            ->assertOk()
            ->assertJsonPath('subscription.status', 'cancelled');
    }

    public function test_superadmin_can_delete_users_and_clubs_but_not_themselves(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $club = Club::create(['name' => 'Delete Club', 'status' => 'active']);
        $user = User::factory()->create(['club_id' => $club->id]);

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/super-admin/users/{$user->id}")
            ->assertNoContent();
        $this->assertModelMissing($user);

        $clubUser = User::factory()->create(['club_id' => $club->id]);
        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/super-admin/clubs/{$club->id}")
            ->assertNoContent();

        $this->assertModelMissing($club);
        $this->assertNull($clubUser->fresh()->club_id);

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/super-admin/users/{$admin->id}")
            ->assertUnprocessable();
        $this->assertModelExists($admin);
    }

    public function test_regular_user_cannot_use_superadmin_management_actions(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/super-admin/users/{$target->id}")
            ->assertForbidden();
    }

    private function createPlan(string $slug, int $maxTeams, int $maxPlayers): SubscriptionPlan
    {
        return SubscriptionPlan::query()->updateOrCreate(
            ['slug' => $slug],
            [
                'name' => ucfirst($slug),
                'price' => $maxTeams * 10,
                'max_teams' => $maxTeams,
                'max_players' => $maxPlayers,
                'features' => ['teams', 'players'],
                'is_active' => true,
            ],
        );
    }
}
