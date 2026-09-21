<?php

namespace App\Http\Controllers;

use App\Models\MatchDay;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MatchDayController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $matches = MatchDay::with([
            'team.users.playerProfile', // Ključno: Učitava sve igrače tima za zapisnik
            'players.playerProfile'      // Učitava već unete podatke iz match_day_user
        ])
        ->orderBy('scheduled_at', 'desc')
        ->get();

        return response()->json(['data' => $matches]);
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

        $matchDay = MatchDay::create($validated);

        return response()->json(['message' => 'Utakmica uspešno zakazana.', 'data' => $matchDay], 201);
    }

    public function updateStats(Request $request, MatchDay $match): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed,canceled',
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
            'players' => 'array',
            'players.*.id' => 'exists:users,id',
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
        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed,canceled',
        ]);

        $match->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'message' => 'Status meča uspešno ažuriran.',
            'data' => $match->fresh(['team.users.playerProfile', 'players.playerProfile'])
        ]);
    }

    public function destroy(MatchDay $match): JsonResponse
    {
        $match->players()->detach(); // Uklanja sve veze u pivot tabeli
        $match->delete();

        return response()->json([
            'message' => 'Meč uspešno obrisan.'
        ]);
    }
}
