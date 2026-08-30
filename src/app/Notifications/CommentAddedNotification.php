<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CommentAddedNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly Comment $comment) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->comment->task_id,
            'comment_id' => $this->comment->id,
            'message' => "Novi komentar na zadatku od strane korisnika #{$this->comment->user_id}",
            'content' => $this->comment->content,
        ];
    }
}
