<?php

namespace App\Http\Controllers;

use App\Models\MatchDay;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MatchDayController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = MatchDay::with([
            'team.members.playerProfile',
            'players.playerProfile',
        ])->orderBy('scheduled_at', 'desc');

        if (!$request->user()->isSuperAdmin()) {
            $query->whereHas('team', fn ($teamQuery) => $teamQuery->where('club_id', $request->user()->club_id));
        }

        return response()->json(['data' => $query->get()]);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'opponent' => 'required|string|max:255',
            'is_home' => 'required|boolean',
            'scheduled_at' => 'required|date',
            'location' => 'nullable|string|max:255',
        ]);

        $team = Team::findOrFail($validated['team_id']);
        abort_unless($request->user()->isSuperAdmin() || (int) $team->club_id === (int) $request->user()->club_id, 403);

        $matchDay = MatchDay::create($validated);

        return response()->json(['message' => 'Utakmica uspešno zakazana.', 'data' => $matchDay], 201);
    }

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
                \Illuminate\Validation\Rule::exists('users', 'id')
                    ->where('club_id', $match->team->club_id),
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

        // 2. Ažuriramo igrače u pivot tabeli
        if (isset($validated['players'])) {
            foreach ($validated['players'] as $playerData) {
                $match->players()->syncWithoutDetaching([
                    $playerData['id'] => [
                        'attended' => $playerData['attended'],
                        'goals' => $playerData['goals'],
                        'assists' => $playerData['assists'],
                    ]
                ]);
            }
        }

        return response()->json([
            'message' => 'Zapisnik je uspešno sačuvan!',
            'data' => $match->fresh(['team', 'players'])
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

        return response()->json([
            'message' => 'Status meča uspešno ažuriran.',
            'data' => $match->fresh(['team.members.playerProfile', 'players.playerProfile'])
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
        $match->players()->detach(); // Uklanja sve veze u pivot tabeli
        $match->delete();

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
