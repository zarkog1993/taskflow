<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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
            'plan_type' => 'standard',
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
            'plan_type' => 'standard',
            'max_teams' => 5,
            'max_players' => 150,
            'status' => 'pending',
        ]);
    }

    public function test_registration_link_loads_plans_and_accepts_a_selection(): void
    {
        Mail::fake();

        $registration = $this->postJson('/api/register', [
            'name' => 'Club Owner',
            'club_name' => 'FK Onboarding',
            'email' => 'onboarding@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertCreated();

        $onboardingUrl = $registration->json('data.onboarding_url');
        $token = basename(parse_url($onboardingUrl, PHP_URL_PATH));

        $this->getJson("/api/onboarding/{$token}")
            ->assertOk()
            ->assertJsonPath('club.name', 'FK Onboarding')
            ->assertJsonCount(3, 'plans')
            ->assertJsonPath('plans.0.slug', 'basic')
            ->assertJsonPath('plans.0.price', 10)
            ->assertJsonPath('plans.0.features.0', 'club_profile')
            ->assertJsonPath('plans.0.features.1', 'players')
            ->assertJsonPath('plans.0.features.2', 'teams')
            ->assertJsonPath('plans.1.slug', 'standard')
            ->assertJsonPath('plans.1.price', 20)
            ->assertJsonPath('plans.2.slug', 'premium')
            ->assertJsonPath('plans.2.price', 50)
            ->assertJsonFragment(['slug' => 'basic']);

        $this->assertDatabaseHas('subscription_plans', [
            'slug' => 'pro',
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('subscription_plans', [
            'slug' => 'unlimited',
            'is_active' => false,
        ]);

        $this->postJson("/api/onboarding/{$token}/select", [
            'plan_type' => 'basic',
        ])->assertAccepted();

        $user = User::where('email', 'onboarding@example.com')->firstOrFail();

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'club_id' => $user->club_id,
            'plan_type' => 'basic',
            'status' => 'pending',
        ]);

        $subscription = $user->subscription()->firstOrFail();
        $this->assertSame(['club_profile', 'players', 'teams'], $subscription->features);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/subscriptions/{$subscription->id}/status", [
                'status' => 'active',
            ])
            ->assertOk()
            ->assertJsonPath('subscription.features.0', 'club_profile');
    }

    public function test_plan_catalog_reads_active_database_values_without_mutating_plans(): void
    {
        $basicPlan = SubscriptionPlan::where('slug', 'basic')->firstOrFail();
        $standardPlan = SubscriptionPlan::where('slug', 'standard')->firstOrFail();
        $legacyPlan = SubscriptionPlan::where('slug', 'pro')->firstOrFail();

        $basicPlan->update([
            'name' => 'Custom Basic',
            'price' => 15,
            'max_teams' => 2,
            'features' => ['club_profile', 'teams'],
        ]);
        $standardPlan->update(['is_active' => false]);
        $legacyPlan->update(['name' => 'Legacy Pro']);

        $this->getJson('/api/subscription-plans')
            ->assertOk()
            ->assertJsonCount(2, 'plans')
            ->assertJsonPath('plans.0.slug', 'basic')
            ->assertJsonPath('plans.0.name', 'Custom Basic')
            ->assertJsonPath('plans.0.price', 15)
            ->assertJsonPath('plans.0.max_teams', 2)
            ->assertJsonPath('plans.1.slug', 'premium');

        $this->assertDatabaseHas('subscription_plans', [
            'id' => $basicPlan->id,
            'name' => 'Custom Basic',
            'price' => 15,
            'max_teams' => 2,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('subscription_plans', [
            'id' => $standardPlan->id,
            'is_active' => false,
        ]);
        $this->assertDatabaseHas('subscription_plans', [
            'id' => $legacyPlan->id,
            'name' => 'Legacy Pro',
            'is_active' => false,
        ]);
    }

    public function test_inactive_plan_cannot_be_selected_during_onboarding(): void
    {
        Mail::fake();
        $this->postJson('/api/register', [
            'name' => 'Club Owner',
            'club_name' => 'FK Inactive Plan',
            'email' => 'inactive-plan@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertCreated();

        $user = User::where('email', 'inactive-plan@example.com')->firstOrFail();
        $user->onboarding_token_expires_at = now()->addDay();
        $user->save();
        $club = $user->club;
        $token = 'inactive-plan-token';
        $club->update([
            'onboarding_token_hash' => hash('sha256', $token),
            'onboarding_token_expires_at' => now()->addDay(),
        ]);
        SubscriptionPlan::where('slug', 'basic')->update(['is_active' => false]);

        $this->postJson("/api/onboarding/{$token}/select", [
            'plan_type' => 'basic',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('plan_type');

        $this->assertDatabaseMissing('subscriptions', ['user_id' => $user->id]);
    }

    public function test_pending_legacy_subscription_can_still_be_approved(): void
    {
        $club = Club::create(['name' => 'Legacy Club', 'status' => 'pending_subscription']);
        $owner = User::factory()->create(['club_id' => $club->id]);
        $legacyPlan = SubscriptionPlan::where('slug', 'pro')->firstOrFail();
        $subscription = Subscription::create([
            'user_id' => $owner->id,
            'club_id' => $club->id,
            'subscription_plan_id' => $legacyPlan->id,
            'plan_type' => 'pro',
            'status' => 'pending',
            'max_teams' => $legacyPlan->max_teams,
            'max_players' => $legacyPlan->max_players,
            'features' => $legacyPlan->features,
            'price' => $legacyPlan->price,
        ]);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/subscriptions/{$subscription->id}/status", [
                'status' => 'active',
            ])
            ->assertOk()
            ->assertJsonPath('subscription.plan_type', 'pro')
            ->assertJsonPath('subscription.status', 'active');

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'plan_type' => 'pro',
            'status' => 'active',
        ]);
    }
}