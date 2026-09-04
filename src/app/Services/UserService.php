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
            'password' => bcrypt($data['password']),
        ]);

        if (!empty($data['player_profile'])) {
            $user->playerProfile()->create([
                'jersey_number' => $data['player_profile']['jersey_number'] ?? null,
                'primary_position' => $data['player_profile']['primary_position'] ?? 'CM',
                'preferred_foot' => $data['player_profile']['preferred_foot'] ?? 'right',
                'category' => $data['player_profile']['category'] ?? 'seniori',
                'seniority' => $data['player_profile']['seniority'] ?? 'senior',
                'fitness_status' => $data['player_profile']['fitness_status'] ?? 'fit',
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
