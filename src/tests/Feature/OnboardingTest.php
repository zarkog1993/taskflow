<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Role;
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
            ->assertJsonFragment(['slug' => 'basic']);

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
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin, 'sanctum')
            ->patchJson("/api/subscriptions/{$subscription->id}/status", [
                'status' => 'active',
            ])
            ->assertOk()
            ->assertJsonPath('subscription.features.0', 'club_profile');
    }
}