<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $taskService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['status', 'priority', 'due_date']);
        $tasks = $this->taskService->getPaginatedForUser($request->user(), $filters);

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request): \Illuminate\Http\JsonResponse
    {
        if (auth()->user()->cannot('create', Task::class)) {
            abort(403, 'Unauthorized action.');
        }

        $task = $this->taskService->createForUser($request->user(), $request->validated());

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Task $task): TaskResource
    {
        Gate::authorize('view', $task);

        return new TaskResource($task->load(['user', 'assignedUser']));
    }

    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        Gate::authorize('update', $task);

        $updatedTask = $this->taskService->update($task, $request->validated());

        return new TaskResource($updatedTask->load(['user', 'assignedUser']));
    }

    public function destroy(Task $task): Response
    {
        Gate::authorize('delete', $task);

        $this->taskService->delete($task);

        return response()->noContent();
    }
}
