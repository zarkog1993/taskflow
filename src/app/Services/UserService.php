<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return User::with('roles')->paginate();
    }

    public function store(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $user;
    }

    public function updateRoles(User $user, array $roleIds): User
    {
        $user->roles()->sync($roleIds);

        return $user->load('roles');
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
