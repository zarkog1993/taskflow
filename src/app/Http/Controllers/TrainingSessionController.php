<?php

namespace App\Http\Controllers;

use App\Models\TrainingSession;
use App\Models\User;
use App\Notifications\TrainingScheduledNotification; // <-- Sastavni uvoz!
use App\Services\TrainingSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification; // <-- Sastavni uvoz!

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
    public function index(Request $request)
    {
        Gate::authorize('viewAny', TrainingSession::class);

        return response()->json(
            $this->sessionService->getPaginatedSessions($request->user())
        );
    }

    /**
     * Store a new training session and send notifications with .ics file.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        Gate::authorize('create', TrainingSession::class);

        $session = $this->sessionService->store($request->validated(), $request->user());

        return response()->json($session, 201);
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
            'player_ids' => 'nullable|array',
            'player_ids.*' => 'exists:users,id'
        ]);

        $trainingSession->users()->sync($validated['player_ids'] ?? []);

        return response()->json([
            'message' => 'Prisustvo uspešno sačuvano.',
            'data' => $trainingSession->fresh(['users.playerProfile'])
        ]);
    }

    public function handleRsvp(TrainingSession $session, User $user, string $status): RedirectResponse
    {
        $isAttending = ($status === 'attended');

        // Ažuriranje prisustva u pivot tabeli
        $session->users()->syncWithoutDetaching([
            $user->id => ['attended' => $isAttending]
        ]);

        // Preusmeravanje na Vue frontend potvrdu
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        $encodedTitle = urlencode($session->title);

        return redirect()->away("{$frontendUrl}/rsvp-confirmation?status={$status}&event={$encodedTitle}");
    }

    /**
     * Delete a training session.
     * @param TrainingSession $trainingSession
     * @return JsonResponse
     */
    public function destroy(TrainingSession $trainingSession): JsonResponse
    {
        // Brisanje zavisnosti u pivot tabeli i samog treninga
        $trainingSession->users()->detach();
        $trainingSession->delete();

        return response()->json(['message' => 'Trening je uspešno obrisan.']);
    }
}
