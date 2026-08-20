<?php

namespace Tests\Feature;

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

        // Kreiramo i autentifikujemo korisnika pre svakog testa
        $this->authUser = User::factory()->create();
        $this->actingAs($this->authUser, 'sanctum');
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
            'links',
            'meta'
        ]);
    }

    public function test_authenticated_user_can_create_user(): void
    {
        $payload = [
            'name' => 'Novi Korisnik',
            'email' => 'novi2@testmail.com',
            'password' => 'Lozinka123!',
            'password_confirmation' => 'Lozinka123!'
        ];

        $response = $this->postJson('/api/users', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'novi2@testmail.com'
        ]);
    }

    public function test_authenticated_user_can_see_single_user(): void
    {
        $targetUser = User::factory()->create();

        $response = $this->getJson("/api/users/{$targetUser->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => $targetUser->id,
                'email' => $targetUser->email
            ]
        ]);
    }

    public function test_authenticated_user_can_update_user_without_changing_email(): void
    {
        $targetUser = User::factory()->create([
            'name' => 'Staro Ime',
            'email' => 'staro@testmail.com'
        ]);

        $updatePayload = [
            'name' => 'Novo Novo Ime',
            'email' => 'staro@testmail.com' // Isti email – testira Rule::unique()->ignore()
        ];

        $response = $this->putJson("/api/users/{$targetUser->id}", $updatePayload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'name' => 'Novo Novo Ime'
        ]);
    }

    public function test_authenticated_user_can_delete_user(): void
    {
        $targetUser = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$targetUser->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id
        ]);
    }
}