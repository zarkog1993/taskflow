<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Priprema: Kreiramo korisnika i zadatak u kom je on kreator
        $this->user = User::factory()->create();
        $this->task = Task::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_with_task_access_can_list_comments(): void
    {
        // ARRANGE: Napravimo 2 komentara za ovaj zadatak
        Comment::factory()->count(2)->create([
            'task_id' => $this->task->id,
        ]);

        // ACT: Pozovemo GET /api/tasks/{task}/comments
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/tasks/{$this->task->id}/comments");

        // ASSERT: Očekujemo 200 OK i 2 komentara u 'data' nizu
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_add_comment_to_task(): void
    {
        // ARRANGE: Pripremimo podatke za slanje
        $payload = [
            'content' => 'Ovo je moj prvi komentar.',
        ];

        // ACT: Pošaljemo POST /api/tasks/{task}/comments
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/tasks/{$this->task->id}/comments", $payload);

        // ASSERT: Očekujemo 201 Created, odgovor sa tekstom i zapis u bazi
        $response->assertStatus(201)
            ->assertJsonPath('data.content', 'Ovo je moj prvi komentar.');

        $this->assertDatabaseHas('comments', [
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'content' => 'Ovo je moj prvi komentar.',
        ]);
    }

    public function test_author_can_update_own_comment(): void
    {
        // ARRANGE: Kreiramo komentar čiji je autor ulogovani korisnik
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
            'content' => 'Stari tekst',
        ]);

        // ACT: Pošaljemo PUT /api/comments/{comment}
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/comments/{$comment->id}", [
                'content' => 'Izmenjeni tekst',
            ]);

        // ASSERT: Očekujemo 200 OK i osvežen zapis u bazi
        $response->assertStatus(200)
            ->assertJsonPath('data.content', 'Izmenjeni tekst');

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Izmenjeni tekst',
        ]);
    }

    public function test_user_cannot_update_others_comment(): void
    {
        // ARRANGE: Napravimo drugi korisnika i NJEGOV komentar
        $otherUser = User::factory()->create();
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $otherUser->id,
            'content' => 'Tuđi komentar',
        ]);

        // ACT: Naš korisnik pokušava da izmeni tuđ komentar
        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/comments/{$comment->id}", [
                'content' => 'Hakovano',
            ]);

        // ASSERT: Očekujemo 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_author_can_delete_own_comment(): void
    {
        // ARRANGE
        $comment = Comment::factory()->create([
            'task_id' => $this->task->id,
            'user_id' => $this->user->id,
        ]);

        // ACT: Pošaljemo DELETE /api/comments/{comment}
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/comments/{$comment->id}");

        // ASSERT: Očekujemo 204 No Content i da komentar ne postoji više u bazi
        $response->assertStatus(204);
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_user_without_task_access_cannot_view_comments(): void
    {
        // ARRANGE: Kreiramo zadatak sa kojim naš ulogovani korisnik nema nikakve veze
        $stranger = User::factory()->create();
        $unauthorizedTask = Task::factory()->create([
            'user_id' => $stranger->id,
            'assigned_to' => null,
        ]);

        // ACT
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/tasks/{$unauthorizedTask->id}/comments");

        // ASSERT: Očekujemo 403 Forbidden
        $response->assertStatus(403);
    }
}