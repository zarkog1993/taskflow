<?php

namespace App\Http\Controllers;

use App\Models\TrainingSession;
use App\Models\User;
use App\Models\Team;
use App\Services\EventInvitationService;
use App\Services\TrainingSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TrainingSessionController extends Controller
{
    private TrainingSessionService $sessionService;

    public function __construct(
        TrainingSessionService $sessionService,
        private EventInvitationService $invitationService,
    ) {
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

        $validated = $request->validate([
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'in:training,match,tactical_analysis,fitness'],
            'status' => ['nullable', 'in:planned,in_progress,completed'],
            'scheduled_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'attendees' => ['nullable', 'array'],
            'attendees.*' => [
                'integer',
                \Illuminate\Validation\Rule::exists('players', 'id')->where('team_id', $request->input('team_id')),
            ],
        ]);

        $team = Team::findOrFail($validated['team_id']);
        abort_unless(
            $request->user()->isSuperAdmin()
            || (int) $team->club_id === (int) $request->user()->club_id,
            403,
        );

        $attendeeIds = $validated['attendees'] ?? [];
        unset($validated['attendees']);

        $session = $this->sessionService->store($validated, $request->user());
        $this->invitationService->invite($session, $attendeeIds);

        return response()->json($session->load('invitedPlayers'), 201);
    }

    /**
     * Update the status of a training session.
     * @param Request $request
     * @param TrainingSession $trainingSession
     * @return JsonResponse
     */
    public function updateStatus(Request $request, TrainingSession $trainingSession): JsonResponse
    {
        Gate::authorize('update', $trainingSession);

        $validated = $request->validate([
            'status' => 'required|in:planned,in_progress,completed',
        ]);

        $updated = $this->sessionService->updateStatus($trainingSession, $validated['status']);

        return response()->json(['data' => $updated]);
    }

    public function update(Request $request, TrainingSession $trainingSession): JsonResponse
    {
        Gate::authorize('update', $trainingSession);

        $validated = $request->validate([
            'team_id' => ['sometimes', 'required', 'integer', 'exists:teams,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['sometimes', 'in:training,match,tactical_analysis,fitness'],
            'status' => ['sometimes', 'in:planned,in_progress,completed'],
            'scheduled_at' => ['sometimes', 'required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        if (isset($validated['team_id'])) {
            $team = Team::findOrFail($validated['team_id']);
            abort_unless(
                $request->user()->isSuperAdmin()
                || (int) $team->club_id === (int) $request->user()->club_id,
                403,
            );
            $validated['club_id'] = $team->club_id;
        }

        $trainingSession->update($validated);

        return response()->json(['data' => $trainingSession->fresh(['team', 'creator'])]);
    }

    public function syncAttendance(Request $request, TrainingSession $trainingSession): JsonResponse
    {
        Gate::authorize('update', $trainingSession);

        $validated = $request->validate([
            'player_ids' => 'nullable|array',
            'player_ids.*' => [
                'integer',
                \Illuminate\Validation\Rule::exists('users', 'id')
                    ->where('club_id', $trainingSession->club_id),
            ],
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
        Gate::authorize('delete', $trainingSession);

        // Brisanje zavisnosti u pivot tabeli i samog treninga
        $trainingSession->users()->detach();
        $trainingSession->delete();

        return response()->json(['message' => 'Trening je uspešno obrisan.']);
    }
}
