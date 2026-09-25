<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Team;
use App\Mail\ClubOnboardingMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthService
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $onboardingToken = Str::random(64);
            $club = $user->club()->create([
                'name' => $data['club_name'] ?? $data['name'] . "'s Club",
                'status' => 'pending',
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'country' => $data['country'] ?? null,
                'phone' => $data['phone'] ?? null,
                'onboarding_token_hash' => hash('sha256', $onboardingToken),
                'onboarding_token_expires_at' => now()->addDays(2),
            ]);

            $user->update(['club_id' => $club->id]);

            $team = Team::create([
                'club_id' => $club->id,
                'name' => $club->name,
                'age_group' => 'senior',
            ]);
            $team->members()->syncWithoutDetaching([$user->id]);

            $clubAdmin = Role::where('slug', 'club-admin')->first();
            if ($clubAdmin) {
                $user->roles()->syncWithoutDetaching([$clubAdmin->id]);
            }

            $accessToken = $user->createToken('auth_token')->plainTextToken;
            $onboardingUrl = rtrim(config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173')), '/')
                . '/onboarding/' . $onboardingToken;

            Mail::to($user->email)->send(new ClubOnboardingMail($user, $club, $onboardingUrl));

            return [
                'user' => $user->load(['roles', 'club.teams']),
                'access_token' => $accessToken,
                'onboarding_url' => $onboardingUrl,
            ];
        });
    }

    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Podaci za prijavu nisu ispravni.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'access_token' => $token,
        ];
    }

    public function logout($user): void
    {
        // Briše token koji je trenutno iskorišćen za ovaj zahtev
        $user->currentAccessToken()->delete();
    }
}
