<?php

namespace App\Http\Controllers;

use App\Models\TrainingSession;
use App\Services\TrainingSessionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TrainingSessionController extends Controller
{
    private TrainingSessionService $sessionService;

    public function __construct(TrainingSessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    /**
     * Display a listing of the training sessions.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $sessions = $this->sessionService->getAllSessions($request->user());
        return response()->json(['data' => $sessions]);
    }

    /**
     * Store a new training session.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:training,match,tactical_analysis,fitness',
            'status' => 'nullable|in:planned,in_progress,completed',
            'scheduled_at' => 'required|date',
            'location' => 'nullable|string|max:255',
        ]);

        $session = $this->sessionService->createSession($request->user(), $validated);

        return response()->json(['data' => $session], 201);
    }

    /**
     * Update the status of a training session.
     * @param Request $request
     * @param TrainingSession $trainingSession
     * @return JsonResponse
     */ 
    public function updateStatus(Request $request, TrainingSession $trainingSession): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:planned,in_progress,completed',
        ]);

        $updated = $this->sessionService->updateStatus($trainingSession, $validated['status']);

        return response()->json(['data' => $updated]);
    }

    public function syncAttendance(Request $request, TrainingSession $trainingSession): JsonResponse
    {
        $validated = $request->validate([
            'player_ids' => 'array',
            'player_ids.*' => 'exists:users,id'
        ]);

        $trainingSession->attendees()->sync($validated['player_ids']);

        return response()->json([
            'data' => $trainingSession->load('attendees')
        ]);
    }
}
