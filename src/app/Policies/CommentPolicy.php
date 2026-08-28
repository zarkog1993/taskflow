<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class CommentPolicy
{
    public function view(User $user, Task $task): bool
    {
        return (int) $user->id === (int) $task->user_id || (int) $user->id === (int) $task->assigned_to;
    }

    public function create(User $user, Task $task): bool
    {
        return (int) $user->id === (int) $task->user_id || (int) $user->id === (int) $task->assigned_to;
    }

    public function update(User $user, Task $task): bool
    {
        return (int) $user->id === (int) $task->user_id || (int) $user->id === (int) $task->assigned_to;
    }

    public function delete(User $user, Task $task): bool
    {
        return (int) $user->id === (int) $task->user_id || (int) $user->id === (int) $task->assigned_to;
    }
}
