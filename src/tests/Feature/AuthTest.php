<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function test_user_can_login_successfully(): void
    {
        // Kreiramo korisnika u bazi
        User::factory()->create([
            'email' => 'loginuser@testmail.com',
            'password' => Hash::make('LoginUser123'),
        ]);

        $loginData = [
            'email' => 'loginuser@testmail.com',
            'password' => 'LoginUser123'
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'user' => ['id', 'name', 'email'],
                'access_token',
                'token_type'
            ]
        ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'loginuser@testmail.com',
            'password' => Hash::make('LoginUser123'),
        ]);

        $loginData = [
            'email' => 'loginuser@testmail.com',
            'password' => 'WrongPassword123'
        ];

        $response = $this->postJson('/api/login', $loginData);

        // ValidationException vraća 422 status
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'logoutuser@testmail.com',
            'password' => Hash::make('LogoutUser123'),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Uspešno ste se odjavili.'
        ]);
    }
}
