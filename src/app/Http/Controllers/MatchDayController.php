<?php

namespace App\Http\Controllers;

use App\Models\MatchDay;
use App\Models\Team;
use App\Services\EventInvitationService;
use App\Services\PlayerStatsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MatchDayController extends Controller
{
    public function __construct(
        private EventInvitationService $invitationService,
        private PlayerStatsService $playerStats,
    ) {}

    /**
     * Get a list of match days, optionally including advanced stats for authorized users.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $relations = ['team.players', 'invitedPlayers'];
        if ($request->user()->isSuperAdmin() || $request->user()->hasFeature('advanced_stats')) {
            $relations[] = 'players';
        }

        $query = MatchDay::with($relations)->orderBy('scheduled_at', 'desc');

        if (!$request->user()->isSuperAdmin()) {
            $query->whereHas('team', fn ($teamQuery) => $teamQuery->where('club_id', $request->user()->club_id));
        }

        return response()->json(['data' => $query->get()]);
    }

    /**
     * Store a newly created match day and invite attendees.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'opponent' => 'required|string|max:255',
            'is_home' => 'required|boolean',
            'scheduled_at' => 'required|date',
            'location' => 'nullable|string|max:255',
            'attendees' => 'nullable|array',
            'attendees.*' => [
                'integer',
                \Illuminate\Validation\Rule::exists('players', 'id')->where('team_id', $request->input('team_id')),
            ],
        ]);

        $team = Team::findOrFail($validated['team_id']);
        abort_unless($request->user()->isSuperAdmin() || (int) $team->club_id === (int) $request->user()->club_id, 403);

        $attendeeIds = $validated['attendees'] ?? [];
        unset($validated['attendees']);

        $matchDay = MatchDay::create($validated);
        $this->invitationService->invite($matchDay, $attendeeIds);

        return response()->json(['message' => 'Utakmica uspešno zakazana.', 'data' => $matchDay->load('invitedPlayers')], 201);
    }

    /**
     * Update the statistics for a specific match day.
     * @param Request $request
     * @param MatchDay $match
     * @return JsonResponse
     */
    public function updateStats(Request $request, MatchDay $match): JsonResponse
    {
        $this->authorizeClubAccess($request, $match);
        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed,canceled',
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
            'players' => 'array',
            'players.*.id' => [
                'integer',
                \Illuminate\Validation\Rule::exists('players', 'id')
                    ->where('team_id', $match->team_id),
            ],
            'players.*.attended' => 'boolean',
            'players.*.goals' => 'integer|min:0',
            'players.*.assists' => 'integer|min:0',
        ]);

        // 1. Ažuriramo status i rezultat utakmice
        $match->update([
            'status' => $validated['status'],
            'home_score' => $validated['home_score'],
            'away_score' => $validated['away_score'],
        ]);

        // 2. Ažuriramo sastav i učinak u pivot tabeli (jedan upit umesto N)
        if (isset($validated['players'])) {
            // Igrači koji su bili u zapisniku pre izmene - i njima se mora
            // preračunati statistika ako su izbačeni iz sastava.
            $previousPlayerIds = $match->players()->pluck('players.id')->all();

            $lineup = collect($validated['players'])
                ->mapWithKeys(fn (array $playerData) => [
                    $playerData['id'] => [
                        'attended' => $playerData['attended'] ?? false,
                        'goals' => $playerData['goals'] ?? 0,
                        'assists' => $playerData['assists'] ?? 0,
                    ],
                ])
                ->all();

            $match->players()->sync($lineup);

            $this->playerStats->recalculateForMatch($match, $previousPlayerIds);
        } else {
            // Promena statusa utakmice menja da li se nastupi uopšte računaju.
            $this->playerStats->recalculateForMatch($match);
        }

        return response()->json([
            'message' => 'Zapisnik je uspešno sačuvan!',
            'data' => $match->fresh(['team.players', 'players', 'invitedPlayers']),
        ]);
    }

    public function updateStatus(Request $request, MatchDay $match): JsonResponse
    {
        $this->authorizeClubAccess($request, $match);
        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed,canceled',
        ]);

        $match->update([
            'status' => $validated['status'],
        ]);

        // Status određuje da li se nastupi iz zapisnika računaju u statistiku.
        $this->playerStats->recalculateForMatch($match);

        return response()->json([
            'message' => 'Status meča uspešno ažuriran.',
            'data' => $match->fresh(['team.players', 'players', 'invitedPlayers'])
        ]);
    }

    public function update(Request $request, MatchDay $match): JsonResponse
    {
        $this->authorizeClubAccess($request, $match);

        $validated = $request->validate([
            'team_id' => ['sometimes', 'required', 'integer', 'exists:teams,id'],
            'opponent' => ['sometimes', 'required', 'string', 'max:255'],
            'is_home' => ['sometimes', 'boolean'],
            'scheduled_at' => ['sometimes', 'required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if (isset($validated['team_id'])) {
            $team = Team::findOrFail($validated['team_id']);
            abort_unless(
                $request->user()->isSuperAdmin()
                || (int) $team->club_id === (int) $request->user()->club_id,
                403,
            );
        }

        $match->update($validated);

        return response()->json(['data' => $match->fresh('team')]);
    }

    public function destroy(MatchDay $match): JsonResponse
    {
        $this->authorizeClubAccess(request(), $match);

        // Igrači iz obrisanog zapisnika moraju izgubiti te nastupe iz statistike.
        $affectedPlayerIds = $match->players()->pluck('players.id')->all();

        $match->players()->detach(); // Uklanja sve veze u pivot tabeli
        $match->delete();

        $this->playerStats->recalculateFor($affectedPlayerIds);

        return response()->json([
            'message' => 'Meč uspešno obrisan.'
        ]);
    }

    private function authorizeClubAccess(Request $request, MatchDay $match): void
    {
        $match->loadMissing('team');
        abort_unless(
            $request->user()->isSuperAdmin()
            || (int) $match->team?->club_id === (int) $request->user()->club_id,
            403,
        );
    }
}
