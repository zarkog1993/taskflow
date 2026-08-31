<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Ako je korisnik administrator, dozvoljavamo sve akcije unapred.
     */
    public function before(User $user, string $ability): ?bool
    {
        $isAdmin = $user->getAttribute('is_admin') ?? false;

        if ((bool) $isAdmin) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, User $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasRole('admin') || (int) $user->id === (int) $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return (int) $user->id === (int) $model->id;
    }
}
