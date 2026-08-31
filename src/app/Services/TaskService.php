<?php

namespace App\Services;

use App\Models\Task;
use App\Notifications\TaskAssignedNotification;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaskService
{
    /**
     * Vraća paginirane zadatke koje je korisnik kreirao ili su mu dodeljeni,
     * uz opciono filtriranje po statusu, prioritetu i datumu.
     */
    public function getPaginatedForUser(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Task::query()
            // 1. Autorizacija na nivou upita:
            // Ako NIJE admin, vidi samo svoje zadatke i zadatke koji su mu dodeljeni.
            // Ako JESTE admin (hasRole('admin')), ovaj uslov se preskače i vratiće sve zadatke.
            ->when(!$user->hasRole('admin'), function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                    ->orWhere('assigned_to', $user->id);
                });
            })
            // 2. Opcioni filter po statusu (npr. 'todo', 'in_progress', 'done')
            ->when(!empty($filters['status']), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            // 3. Opcioni filter po prioritetu (npr. 'low', 'medium', 'high')
            ->when(!empty($filters['priority']), function ($query) use ($filters) {
                $query->where('priority', $filters['priority']);
            })
            // 4. Opcioni filter po datumu (npr. '2026-08-25')
            ->when(!empty($filters['due_date']), function ($query) use ($filters) {
                $query->whereDate('due_date', $filters['due_date']);
            })
            ->latest()
            ->paginate($perPage);
    }

    public function createForUser(User $user, array $data): Task
    {
        // Automatski dodeljujemo ulogovanog korisnika kao kreatora
        $data['user_id'] = $user->id;

        $task = Task::create($data);

        if ($task->assigned_to && (int) $task->assigned_to !== (int) $task->user_id) {
            $task->assignedUser?->notify(new TaskAssignedNotification($task));
        }

        return $task;
    }

    public function update(Task $task, array $data): Task
    {
        if ($task->assigned_to && (int) $task->assigned_to !== (int) $task->user_id) {
            $task->assignedUser?->notify(new TaskAssignedNotification($task));
        }
        $task->update($data);

        return $task;
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }
}
