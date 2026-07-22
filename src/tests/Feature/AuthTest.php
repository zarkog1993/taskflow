<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_register_successfully(): void
    {
        $data = [
            'name' => 'DjuraTestZarko',
            'email' => 'Djuratestzarko@testmail.com',
            'password' => 'DjuraTestZGtest123',
            'password_confirmation' => 'DjuraTestZGtest123'
        ];

        $response = $this->postJson('/api/register', $data);

        $this->assertDatabaseHas('users', [
            'email' => 'Djuratestzarko@testmail.com'
        ]);

        $response->assertStatus(201);
    }

    public function test_registration_fails_if_email_already_exists (): void
    {
        $existingUser = [
            'name' => 'ExistingUser',
            'email' => 'Djuratestzarko@testmail.com',
            'password' => 'DjuraTestZGtest123',
            'password_confirmation' => 'DjuraTestZGtest123'
        ];

        $this->postJson('/api/register', $existingUser);

        $response = $this->postJson('/api/register', $existingUser);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }
}
