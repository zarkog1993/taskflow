<?php

namespace App\Http\Controllers;

use App\Models\MatchDay;
use App\Models\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MatchLineupController extends Controller
{
    private const array FORMATIONS = ['4-3-3', '4-4-2', '4-2-3-1', '3-5-2'];

    /**
     * Display the lineup for a specific match day.
     * @param Request $request
     * @param MatchDay $match
     * @return JsonResponse
     */
    public function show(Request $request, MatchDay $match): JsonResponse
    {
        $this->authorizeMatch($request, $match, false);
        $this->ensureScheduled($match);
        $match->load(['team.tactic', 'invitedPlayers']);

        $confirmedPlayers = $match->invitedPlayers
            ->filter(fn (Player $player) => $player->pivot->status === 'accepted'
                && (int) $player->team_id === (int) $match->team_id)
            ->values();
        $confirmedIds = $confirmedPlayers->modelKeys();
        $lineup = $match->lineup;

        if ($lineup) {
            $data = $lineup->toArray();
            $data['positions'] = collect($lineup->positions)
                ->map(fn (array $position) => [
                    ...$position,
                    'player_id' => in_array((int) ($position['player_id'] ?? 0), $confirmedIds, true)
                        ? (int) $position['player_id']
                        : null,
                ])
                ->values();
            $data['bench_player_ids'] = collect($lineup->bench_player_ids ?? [])
                ->map(fn ($playerId) => (int) $playerId)
                ->filter(fn (int $playerId) => in_array($playerId, $confirmedIds, true))
                ->values();
        } else {
            $teamTactic = $match->team?->tactic;
            $data = [
                'formation' => $teamTactic?->formation ?? '4-3-3',
                'positions' => collect($teamTactic?->positions ?? [])
                    ->map(fn (array $position) => [
                        ...$position,
                        'player_id' => in_array((int) ($position['player_id'] ?? 0), $confirmedIds, true)
                            ? (int) $position['player_id']
                            : null,
                    ])
                    ->values(),
                'bench_player_ids' => [],
            ];
        }

        return response()->json([
            'data' => [
                'match' => $match->only(['id', 'opponent', 'is_home', 'scheduled_at', 'location', 'status'])
                    + ['team' => $match->team?->only(['id', 'name'])],
                'lineup' => $data,
                'has_saved_lineup' => $lineup !== null,
                'confirmed_players' => $confirmedPlayers,
            ],
        ]);
    }

    /**
     * Update the lineup for a specific match day.
     * @param Request $request
     * @param MatchDay $match
     * @return JsonResponse
     */
    public function update(Request $request, MatchDay $match): JsonResponse
    {
        $this->authorizeMatch($request, $match, true);
        $this->ensureScheduled($match);

        $validated = $request->validate([
            'formation' => ['required', 'string', Rule::in(self::FORMATIONS)],
            'positions' => ['required', 'array', 'size:11'],
            'positions.*' => ['required', 'array:spot_id,x,y,player_id'],
            'positions.*.spot_id' => ['required', 'integer', 'between:1,11', 'distinct:strict'],
            'positions.*.x' => ['required', 'numeric', 'between:0,100'],
            'positions.*.y' => ['required', 'numeric', 'between:0,100'],
            'positions.*.player_id' => ['present', 'nullable', 'integer'],
            'bench_player_ids' => ['present', 'array', 'max:20'],
            'bench_player_ids.*' => ['required', 'integer', 'distinct:strict'],
        ]);

        $acceptedPlayerIds = $match->invitedPlayers()
            ->wherePivot('status', 'accepted')
            ->where('players.team_id', $match->team_id)
            ->pluck('players.id')
            ->map(fn ($id) => (int) $id);

        $selectedPlayerIds = collect($validated['positions'])
            ->pluck('player_id')
            ->filter(fn ($id) => $id !== null)
            ->merge($validated['bench_player_ids'])
            ->map(fn ($id) => (int) $id);

        if ($selectedPlayerIds->duplicates()->isNotEmpty()) {
            throw ValidationException::withMessages([
                'positions' => 'Igrač može biti postavljen u početni sastav ili među rezerve, ali ne u oba.',
            ]);
        }

        if ($selectedPlayerIds->diff($acceptedPlayerIds)->isNotEmpty()) {
            throw ValidationException::withMessages([
                'positions' => 'Sastav i rezerve mogu se birati samo među igračima koji su potvrdili dolazak.',
            ]);
        }

        $lineup = DB::transaction(fn () => $match->lineup()->updateOrCreate(
            [],
            [
                'formation' => $validated['formation'],
                'positions' => $validated['positions'],
                'bench_player_ids' => array_map('intval', $validated['bench_player_ids']),
            ],
        ));

        return response()->json([
            'message' => 'Predlog sastava je sačuvan.',
            'data' => $lineup,
        ]);
    }

    /**
     * Authorize access to a specific match for viewing or updating.
     * @param Request $request
     * @param MatchDay $match
     * @param bool $forUpdate
     * @return void
     */
    private function authorizeMatch(Request $request, MatchDay $match, bool $forUpdate): void
    {
        $match->loadMissing('team');
        abort_unless($match->team, 404);
        Gate::authorize($forUpdate ? 'update' : 'view', $match->team);
    }

    /**
     * Ensure that the match is scheduled before allowing lineup modifications.
     * @param MatchDay $match
     * @return void
     */
    private function ensureScheduled(MatchDay $match): void
    {
        abort_unless($match->status === 'scheduled', 409, 'Planer je dostupan samo za zakazane utakmice.');
    }
}
