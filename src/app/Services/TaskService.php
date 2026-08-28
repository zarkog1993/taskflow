<?php

namespace App\Services;

use App\Models\Task;
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
            // 1. Osnovna autorizacija na nivou upita (Vidim samo svoje i meni dodeljene)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('assigned_to', $user->id);
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

        return Task::create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }
}
