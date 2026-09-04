<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    public function getAllPaginated(): LengthAwarePaginator
    {
        return User::with(['roles', 'playerProfile'])->latest()->paginate(20);
    }

    public function store(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'] ?? 'password123'),
        ]);

        // Automatski dodeli 'player' ulogu ako je prosleđen profil igrača
        if (isset($data['player_profile'])) {
            $playerRole = Role::where('slug', 'player')->first();
            if ($playerRole) {
                $user->roles()->sync([$playerRole->id]);
            }

            $user->playerProfile()->create([
                'jersey_number' => $data['player_profile']['jersey_number'] ?? null,
                'primary_position' => $data['player_profile']['primary_position'] ?? 'CM',
                'category' => $data['player_profile']['category'] ?? 'seniori',
                'seniority' => $data['player_profile']['seniority'] ?? 'senior',
                'preferred_foot' => $data['player_profile']['preferred_foot'] ?? 'right',
                'fitness_status' => $data['player_profile']['fitness_status'] ?? 'fit',
                'date_of_birth' => $data['player_profile']['date_of_birth'] ?? null,
            ]);
        }

        return $user->load(['roles', 'playerProfile']);
    }

    public function updateRoles(User $user, array $roleIds): User
    {
        $user->roles()->sync($roleIds);
        return $user->load(['roles', 'playerProfile']);
    }
}
