<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;

class CommentPolicy
{
    /**
     * Ako je korisnik administrator, dozvoljavamo sve akcije unapred.
     * @param User $user
     * @param string $ability
     * @return bool|null
     */
    public function before(User $user, string $ability): ?bool
    {
        $isAdmin = $user->getAttribute('is_admin') ?? false;

        if ((bool) $isAdmin) {
            return true;
        }

        return null;
    }

    /**
     * Korisnik moze da vidi sve komentare na zadatku ako je kreator zadatka ili dodeljeni radnik
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function viewAny(User $user, Task $task): bool
    {
        $userId = (int) $user->id;
        $creatorId = (int) ($task->getAttribute('user_id') ?? 0);
        $assignedId = (int) ($task->getAttribute('assigned_to') ?? 0);

        return $userId === $creatorId || $userId === $assignedId;
    }

    /**
     * Korisnik može da komentariše ako ima pristup zadatku
     * @param User $user
     * @param Task $task
     * @return bool
     */
    public function create(User $user, Task $task): bool
    {
        $userId = (int) $user->id;
        $creatorId = (int) ($task->getAttribute('user_id') ?? 0);
        $assignedId = (int) ($task->getAttribute('assigned_to') ?? 0);

        return $userId === $creatorId || $userId === $assignedId;
    }

    /**
     * Samo autor komentara sme da ga izmeni
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    public function update(User $user, Comment $comment): bool
    {
        $userId = (int) $user->id;
        $authorId = (int) ($comment->getAttribute('user_id') ?? 0);

        return $userId === $authorId;
    }

    /**
     * Samo autor komentara sme da ga obriše
     * @param User $user
     * @param Comment $comment
     * @return bool
     */
    public function delete(User $user, Comment $comment): bool
    {
        $userId = (int) $user->id;
        $authorId = (int) ($comment->getAttribute('user_id') ?? 0);

        return $userId === $authorId;
    }
}
