<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
        // 1. Ako e-mail nije unet, generišemo privremeni
        $email = (!empty($data['email']))
            ? $data['email']
            : Str::slug($data['name']) . rand(100, 999) . '@taskflow.local';

        // 2. Proveravamo da li je prosleđena lozinka; ako nije, generišemo nasumičnu
        $password = !empty($data['password'])
            ? Hash::make($data['password'])
            : Hash::make(Str::random(16));

        // 3. Kreiramo User-a
        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => $password,
        ]);

        // 4. Kreiramo profil ako postoje fudbalski parametri
        if (isset($data['primary_position'])) {
            $user->playerProfile()->create([
                'primary_position' => $data['primary_position'],
                'jersey_number' => $data['jersey_number'] ?? null,
                'height' => $data['height'] ?? null,
                'weight' => $data['weight'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'preferred_foot' => $data['preferred_foot'] ?? 'right',
                'seniority' => $data['seniority'] ?? 'Seniori',
            ]);
        }

        if (!empty($data['team_id'])) {
            $user->teams()->sync([$data['team_id']]);
        }

        return $user->load(['roles', 'playerProfile', 'teams']);
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
