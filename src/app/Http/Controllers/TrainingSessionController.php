<?php

namespace App\Http\Controllers;

use App\Models\TrainingSession;
use App\Models\User;
use App\Notifications\TrainingScheduledNotification; // <-- Sastavni uvoz!
use App\Services\TrainingSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
    public function index(Request $request): JsonResponse
    {
        $sessions = TrainingSession::with([
            'users' => function ($query) {
                $query->select('users.id', 'users.name', 'users.email')
                    ->withPivot('attended');
            },
            'team'
        ])->latest('scheduled_at')->get();

        return response()->json(['data' => $sessions]);
    }

    /**
     * Store a new training session and send notifications with .ics file.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'team_id'      => 'required|exists:teams,id',
            'title'        => 'required|string|max:255',
            'scheduled_at' => 'required|date',
            'location'     => 'nullable|string|max:255',
            'attendees'    => 'nullable|array',
            'attendees.*'  => 'exists:users,id',
        ]);

        $validated['created_by'] = auth()->id() ?? $request->user()?->id ?? 1;

        $session = TrainingSession::create($validated);

        // 1. Sinhronizujemo štiklirane igrače u pivot tabelu training_user
        if (!empty($validated['attendees'])) {
            $session->users()->sync($validated['attendees']);
        }

        // 2. Pronalazimo primaoce: prvenstveno selektovani igrači, a ako ih nema onda cela ekipa
        $recipients = $session->users()->get();

        if ($recipients->isEmpty() && $session->team) {
            $recipients = $session->team->users;
        }

        // 3. Slanje mail notifikacije sa .ics prilogom
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new TrainingScheduledNotification($session));
        }

        return response()->json(['data' => $session->load('users')], 201);
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
