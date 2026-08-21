<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
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

    public function view(User $user, Task $task): bool
    {
        return (int) $user->id === (int) $task->user_id || (int) $user->id === (int) $task->assigned_to;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Task $task): bool
    {
        return (int) $user->id === (int) $task->user_id || (int) $user->id === (int) $task->assigned_to;
    }

    public function delete(User $user, Task $task): bool
    {
        // Samo kreator sme da briše svoj zadatak (admin prolazi kroz before)
        return (int) $user->id === (int) $task->user_id;
    }
}
