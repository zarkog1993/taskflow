<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CommentService
{
    /**
     * Vraća sve komentare vezane za zadatak sa učitanim autorima.
     */
    public function getForTask(Task $task): Collection
    {
        return $task->comments()
            ->with('user')
            ->latest()
            ->get();
    }

    public function createForTask(User $user, Task $task, array $data): Model
    {
        return $task->comments()->create([
            'user_id' => $user->id,
            'content' => $data['content'],
        ]);
    }

    public function update(Comment $comment, array $data): Comment
    {
        $comment->update($data);

        return $comment;
    }

    public function delete(Comment $comment): bool
    {
        return $comment->delete();
    }
}
