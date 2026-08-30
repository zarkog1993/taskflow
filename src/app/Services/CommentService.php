<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Notifications\CommentAddedNotification;
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
        $comment = $task->comments()->create([
            'user_id' => $user->id,
            'content' => $data['content'],
        ]);

        // Obaveštavamo kreatora zadatka ako nije sam ostavio komentar
        if ((int) $task->user_id !== (int) $user->id) {
            $task->user?->notify(new CommentAddedNotification($comment));
        }

        // Ako su različiti, obaveštavamo i dodeljenog korisnika
        if ($task->assigned_to && (int) $task->assigned_to !== (int) $user->id && (int) $task->assigned_to !== (int) $task->user_id) {
            $task->assignedUser?->notify(new CommentAddedNotification($comment));
        }
        return $comment;
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
