<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Kreiramo osnovnog korisnika za testiranje
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_list_their_created_or_assigned_tasks(): void
    {
        // 1. Kreiramo zadatak gde je korisnik kreator
        Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Korisnikov zadatak',
        ]);

        // 2. Kreiramo zadatak gde je korisnik dodeljen
        $otherUser = User::factory()->create();
        Task::factory()->create([
            'user_id' => $otherUser->id,
            'assigned_to' => $this->user->id,
            'title' => 'Dodeljen zadatak',
        ]);

        // 3. Kreiramo tuđ zadatak u kom ovaj korisnik ne učestvuje
        Task::factory()->create([
            'user_id' => $otherUser->id,
            'assigned_to' => null,
            'title' => 'Tuđ zadatak',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_user_can_filter_tasks_by_status(): void
    {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'todo',
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'done',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/tasks?status=done');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.status', 'done');
    }

    public function test_authenticated_user_can_create_task(): void
    {
        $payload = [
            'title' => 'Novi zadatak',
            'description' => 'Detaljan opis zadatka',
            'priority' => 'high',
            'status' => 'todo',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/tasks', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Novi zadatak');

        $this->assertDatabaseHas('tasks', [
            'user_id' => $this->user->id,
            'title' => 'Novi zadatak',
            'priority' => 'high',
        ]);
    }

    public function test_user_can_view_own_task(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $task->id);
    }

    public function test_user_cannot_view_unauthorized_task(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    public function test_assigned_user_can_update_task(): void
    {
        $creator = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $creator->id,
            'assigned_to' => $this->user->id,
            'status' => 'todo',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/tasks/{$task->id}", [
                'status' => 'in_progress',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'in_progress');
    }

    public function test_assigned_user_cannot_delete_task(): void
    {
        $creator = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $creator->id,
            'assigned_to' => $this->user->id,
        ]);

        // Po našoj polisi, samo kreator i admin mogu da brišu
        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }

    public function test_creator_can_delete_own_task(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
