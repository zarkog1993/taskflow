<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Team;
use App\Models\MatchDay;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $clubId = $user->club_id ?? $user->academy_id;

        // Fetch timova kluba
        $teams = Team::where('club_id', $clubId)->get(['id', 'name', 'age_group']);

        // Prikupljanje igrača sa njihovim statistikama
        $players = Player::where('club_id', $clubId)
            ->with(['team:id,name'])
            ->get()
            ->map(function ($player) {
                // Izračunavanje prisustva na treninzima (u procentima)
                $totalTrainings = DB::table('training_user')
                    ->where('user_id', $player->user_id ?? $player->id)
                    ->count();

                $attendedTrainings = DB::table('training_user')
                    ->where('user_id', $player->user_id ?? $player->id)
                    ->where('attended', true)
                    ->count();

                $attendanceRate = $totalTrainings > 0 
                    ? round(($attendedTrainings / $totalTrainings) * 100) 
                    : 0;

                $matchesCount = $player->matches_count ?? 1;
                $goals = $player->goals ?? 0;
                $assists = $player->assists ?? 0;

                // Indeks korisnosti: (Golovi + Asistencije) / Broj utakmica
                $performanceIndex = round(($goals + $assists) / max($matchesCount, 1), 2);

                return [
                    'id' => $player->id,
                    'name' => $player->name,
                    'jersey_number' => $player->jersey_number,
                    'primary_position' => $player->primary_position,
                    'team_id' => $player->team_id,
                    'team_name' => $player->team?->name ?? 'Bez tima',
                    'matches_count' => $matchesCount,
                    'goals' => $goals,
                    'assists' => $assists,
                    'attendance_rate' => $attendanceRate,
                    'performance_index' => $performanceIndex,
                ];
            });

        // Ukupni agregati
        $totalMatches = MatchDay::whereIn('team_id', $teams->pluck('id'))->count();
        $totalGoals = $players->sum('goals');
        $totalAssists = $players->sum('assists');
        $avgAttendance = $players->avg('attendance_rate') ?? 0;

        return response()->json([
            'teams' => $teams,
            'players' => $players->sortByDesc('performance_index')->values(),
            'summary' => [
                'total_matches' => $totalMatches,
                'total_goals' => $totalGoals,
                'total_assists' => $totalAssists,
                'goals_per_match' => $totalMatches > 0 ? round($totalGoals / $totalMatches, 2) : 0,
                'avg_attendance_rate' => round($avgAttendance, 1),
            ]
        ]);
    }
}
