<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    private User $authUser;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Stvaramo klub
        $club = Club::factory()->create([
            'status' => 'approved'
        ]);

        // 2. Stvaramo autentificiranog korisnika dodijeljenog tom klubu
        $this->authUser = User::factory()->create([
            'club_id' => $club->id,
        ]);

        // 3. Stvaramo aktivnu pretplatu s 'advanced_management' mogućnosti
        Subscription::factory()->create([
            'club_id' => $club->id,
            'user_id' => $this->authUser->id,
            'status' => 'active',
            'features' => ['advanced_management', 'players', 'teams', 'matches']
        ]);

        // Postavljamo korisnika kao ulogiranog za testove
        $this->actingAs($this->authUser);
    }

    public function test_authenticated_user_can_get_paginated_users_list(): void
    {
        User::factory()->count(5)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'email', 'created_at']
            ],
        ]);
    }

    public function test_authenticated_user_cannot_create_user(): void
    {
        $payload = [
            'name'             => 'Novi Korisnik',
            'email'            => 'novi2@testmail.com',
            'primary_position' => 'CM',
            'seniority'        => 'senior', // <-- Dodaj dozvoljenu vrednost koja prolazi CHECK ogranicenje
        ];

        $response = $this->actingAs($this->authUser)->postJson('/api/users', $payload);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', [
            'email' => 'novi2@testmail.com'
        ]);
    }

    public function test_authenticated_user_can_see_their_own_account(): void
    {
        $response = $this->getJson("/api/users/{$this->authUser->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $this->authUser->id,
                'email' => $this->authUser->email
            ]
        ]);
    }

    public function test_authenticated_user_cannot_see_another_clubs_user(): void
    {
        $otherClub = Club::factory()->create();
        $targetUser = User::factory()->create(['club_id' => $otherClub->id]);

        $this->getJson("/api/users/{$targetUser->id}")
            ->assertForbidden();
    }

    public function test_club_admin_cannot_create_a_super_admin(): void
    {
        $clubAdmin = $this->createClubAdmin();
        Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);

        $this->actingAs($clubAdmin, 'sanctum')
            ->postJson('/api/users', [
                'name' => 'Privileged User',
                'email' => 'privileged@example.com',
                'password' => 'password123',
                'role' => 'admin',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'privileged@example.com']);
    }

    public function test_club_admin_cannot_assign_a_new_user_to_another_club(): void
    {
        $clubAdmin = $this->createClubAdmin();
        $otherClub = Club::factory()->create();
        Role::firstOrCreate(['slug' => 'player'], ['name' => 'Player']);

        $this->actingAs($clubAdmin, 'sanctum')
            ->postJson('/api/users', [
                'name' => 'New Player',
                'email' => 'new-player@example.com',
                'password' => 'password123',
                'club_id' => $otherClub->id,
                'role' => 'player',
            ])
            ->assertCreated();

        $this->assertDatabaseHas('users', [
            'email' => 'new-player@example.com',
            'club_id' => $clubAdmin->club_id,
        ]);
    }

    public function test_authenticated_user_can_update_user_without_changing_email(): void
    {
        $updatePayload = [
            'name' => 'Novo Novo Ime',
            'email' => $this->authUser->email // Ažurira SEBE
        ];

        $response = $this->putJson("/api/users/{$this->authUser->id}", $updatePayload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $this->authUser->id,
            'name' => 'Novo Novo Ime'
        ]);
    }

    public function test_authenticated_user_can_delete_user(): void
    {
        $response = $this->deleteJson("/api/users/{$this->authUser->id}"); // Briše SEBE

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', [
            'id' => $this->authUser->id
        ]);
    }

    public function test_authenticated_user_cannot_delete_another_user(): void
    {
        $anotherUser = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$anotherUser->id}");

        $response->assertStatus(403); // Forbidden
    }

    public function test_user_cannot_update_another_user(): void
    {
        $anotherUser = User::factory()->create();

        $response = $this->putJson("/api/users/{$anotherUser->id}", [
            'name' => 'Neovlašćena Izmena',
        ]);

        $response->assertStatus(403);
    }

    public function test_user_can_update_their_own_profile(): void
    {
        $response = $this->putJson("/api/users/{$this->authUser->id}", [
            'name' => 'Novo Moje Ime',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $this->authUser->id,
            'name' => 'Novo Moje Ime',
        ]);
    }

    public function test_admin_can_delete_any_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $targetUser = User::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/users/{$targetUser->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);
    }

    private function createClubAdmin(): User
    {
        $clubAdminRole = Role::firstOrCreate(
            ['slug' => 'club-admin'],
            ['name' => 'Club Admin'],
        );
        $clubAdmin = User::factory()->create(['club_id' => $this->authUser->club_id]);
        $clubAdmin->roles()->attach($clubAdminRole);

        Subscription::create([
            'user_id' => $clubAdmin->id,
            'club_id' => $clubAdmin->club_id,
            'plan_type' => 'standard',
            'status' => 'active',
            'max_teams' => 5,
            'max_players' => 150,
            'features' => ['players'],
            'ends_at' => now()->addMonth(),
        ]);

        return $clubAdmin;
    }
}
