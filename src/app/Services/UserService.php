<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    public function getAllPaginated(): LengthAwarePaginator
    {
        $user = auth()->user();

        // Ako je korisnik admin (preko is_admin kolone ILI uloge 'admin')
        if ($user && ($user->is_admin || $user->hasRole('admin'))) {
            return User::with(['roles', 'playerProfile', 'teams'])
                ->latest()
                ->paginate(50);
        }

        // Za ostale (Team Admin) - prikazuje samo igrače iz njihovih timova
        $teamIds = $user ? $user->teams()->pluck('teams.id') : [];

        return User::whereHas('teams', function ($q) use ($teamIds) {
            $q->whereIn('teams.id', $teamIds);
        })
            ->with(['roles', 'playerProfile', 'teams'])
            ->latest()
            ->paginate(50);
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

    public function update(User $user, array $data): User
    {
        $user->update(array_filter([
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
        ]));

        return $user->fresh(['roles', 'playerProfile']);
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }

    public function updateRoles(User $user, array $roleIds): User
    {
        $user->roles()->sync($roleIds);
        return $user->load(['roles', 'playerProfile']);
    }
}
