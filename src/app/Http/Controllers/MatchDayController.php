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
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'players' => 'array',
            'players.*.id' => 'exists:users,id',
            'players.*.attended' => 'boolean',
            'players.*.goals' => 'integer|min:0',
            'players.*.assists' => 'integer|min:0',
        ]);

        // Ako je uneta krajnji rezultat ili je status eksplicitno promenjen
        $match->update([
            'status' => $validated['status'],
            'home_score' => $validated['home_score'],
            'away_score' => $validated['away_score'],
        ]);

        if (isset($validated['players'])) {
            $syncData = [];
            foreach ($validated['players'] as $p) {
                $syncData[$p['id']] = [
                    'attended' => $p['attended'] ?? false,
                    'goals' => $p['goals'] ?? 0,
                    'assists' => $p['assists'] ?? 0,
                ];
            }
            $match->players()->sync($syncData);
        }

        return response()->json([
            'message' => 'Zapisnik uspešno ažuriran.',
            'data' => $match->fresh(['team.users.playerProfile', 'players.playerProfile'])
        ]);
    }
}
