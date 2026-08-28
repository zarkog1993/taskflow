<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Task;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function __construct(private readonly CommentService $commentService) {}

    public function index(Task $task): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', [Comment::class, $task]);

        $comments = $this->commentService->getForTask($task);

        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request, Task $task): JsonResponse
    {
        Gate::authorize('create', [Comment::class, $task]);

        $comment = $this->commentService->createForTask(
            $request->user(),
            $task,
            $request->validated()
        );

        return (new CommentResource($comment->load('user')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateCommentRequest $request, Comment $comment): CommentResource
    {
        Gate::authorize('update', $comment);

        $updatedComment = $this->commentService->update($comment, $request->validated());

        return new CommentResource($updatedComment->load('user'));
    }

    public function destroy(Comment $comment): Response
    {
        Gate::authorize('delete', $comment);

        $this->commentService->delete($comment);

        return response()->noContent();
    }
}