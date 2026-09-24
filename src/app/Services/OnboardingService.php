<?php

namespace App\Services;

use App\Models\Club;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OnboardingService
{
    /**
     * Konfiguracija limita po paketima
     */
    protected array $planLimits = [
        'basic' => [
            'max_teams' => 1,
            'max_players' => 25,
        ],
        'pro' => [
            'max_teams' => 5,
            'max_players' => 150,
        ],
        'unlimited' => [
            'max_teams' => 999,
            'max_players' => 9999,
        ],
    ];

    /**
     * Zpocinje i zavrsava onboarding: kreira Klub, dodeljuje Pretplatu i ulogu club-admin korisniku.
     */
    public function completeOnboarding(User $user, array $data): Club
    {
        return DB::transaction(function () use ($user, $data) {
            $planType = $data['plan_type'];
            $limits = $this->planLimits[$planType] ?? $this->planLimits['basic'];

            // 1. Kreiranje kluba
            $club = Club::create([
                'name' => $data['club_name'],
            ]);

            // 2. Ažuriranje korisnika (povezivanje sa klubom)
            $user->update([
                'club_id' => $club->id,
            ]);

            // 3. Dodeljivanje uloge 'club-admin'
            $role = Role::where('slug', 'club-admin')->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }

            // 4. Kreiranje pretplate
            Subscription::create([
                'user_id' => $user->id,
                'plan_type' => $planType,
                'status' => 'active',
                'max_teams' => $limits['max_teams'],
                'max_players' => $limits['max_players'],
                'ends_at' => now()->addMonth(), // Mesečna pretplata
            ]);

            return $club->load(['users']);
        });
    }
}
