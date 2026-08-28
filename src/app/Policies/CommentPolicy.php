<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;

class CommentPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        $isAdmin = $user->getAttribute('is_admin') ?? false;

        if ((bool) $isAdmin) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user, Task $task): bool
    {
        // Korisnik vidi komentare ako je kreator zadatka ili dodeljeni radnik
        $userId = (int) $user->id;
        $creatorId = (int) ($task->getAttribute('user_id') ?? 0);
        $assignedId = (int) ($task->getAttribute('assigned_to') ?? 0);

        return $userId === $creatorId || $userId === $assignedId;
    }

    public function create(User $user, Task $task): bool
    {
        // Korisnik može da komentariše ako ima pristup zadatku
        $userId = (int) $user->id;
        $creatorId = (int) ($task->getAttribute('user_id') ?? 0);
        $assignedId = (int) ($task->getAttribute('assigned_to') ?? 0);

        return $userId === $creatorId || $userId === $assignedId;
    }

    public function update(User $user, Comment $comment): bool
    {
        // Samo autor komentara sme da ga izmeni
        $userId = (int) $user->id;
        $authorId = (int) ($comment->getAttribute('user_id') ?? 0);

        return $userId === $authorId;
    }

    public function delete(User $user, Comment $comment): bool
    {
        // Samo autor komentara sme da ga obriše
        $userId = (int) $user->id;
        $authorId = (int) ($comment->getAttribute('user_id') ?? 0);

        return $userId === $authorId;
    }
}
