<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Notifications\CommentAddedNotification;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_is_sent_when_task_is_assigned_to_user(): void
    {
        Notification::fake();

        $creator = User::factory()->create();
        $assignedUser = User::factory()->create();

        // Kreiramo zadatak i dodeljujemo ga drugom korisniku
        $payload = [
            'title' => 'Test Zastatak',
            'description' => 'Opis zadatka',
            'status' => 'todo',
            'priority' => 'high',
            'assigned_to' => $assignedUser->id,
        ];

        $this->actingAs($creator, 'sanctum')
            ->postJson('/api/tasks', $payload)
            ->assertStatus(201);

        // Proveravamo da li je obaveštenje poslato dodeljenom korisniku
        Notification::assertSentTo(
            [$assignedUser],
            TaskAssignedNotification::class
        );
    }

    public function test_notification_is_sent_to_task_creator_when_comment_is_added(): void
    {
        Notification::fake();

        $creator = User::factory()->create();
        $commenter = User::factory()->create();
    
        // ZVEZDICA: Dodeljujemo zadatak komentatoru (assigned_to) da bi imao prava da ga komentariše
        $task = Task::factory()->create([
            'user_id' => $creator->id,
            'assigned_to' => $commenter->id 
        ]);

        $payload = [
            'content' => 'Ovo je novi komentar.',
        ];

        $this->actingAs($commenter, 'sanctum')
            ->postJson("/api/tasks/{$task->id}/comments", $payload)
            ->assertStatus(201);

        // Proveravamo da li je kreator zadatka dobio obaveštenje
        Notification::assertSentTo(
            [$creator],
            CommentAddedNotification::class
        );
    }

    public function test_user_can_fetch_their_notifications(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        // Ručno kreiramo obaveštenje u bazi za korisnika
        $user->notify(new TaskAssignedNotification($task));

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/notifications')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.data');
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();

        $user->notify(new TaskAssignedNotification($task));
        $notification = $user->unreadNotifications->first();

        $this->actingAs($user, 'sanctum')
            ->patchJson("/api/notifications/{$notification->id}/read")
            ->assertStatus(200);

        $this->assertEquals(0, $user->fresh()->unreadNotifications->count());
    }
}
