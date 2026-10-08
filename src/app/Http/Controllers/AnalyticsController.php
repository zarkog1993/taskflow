<?php

namespace App\Http\Controllers;

use App\Models\MatchDay;
use App\Models\Player;
use App\Models\Team;
use App\Models\TrainingSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Status pozivnice koji se računa kao dolazak na trening.
     */
    private const string ATTENDED_STATUS = 'accepted';

    /**
     * Get analytics data for the specified team and date range.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'team_id' => 'nullable|integer|exists:teams,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $user = $request->user();
        $clubId = $user->club_id ?? $user->academy_id;

        $teamId = $validated['team_id'] ?? null;
        $dateFrom = $validated['date_from'] ?? null;
        $dateTo = $validated['date_to'] ?? null;

        $teams = Team::where('club_id', $clubId)->get(['id', 'name', 'age_group']);

        // Treninzi koji ulaze u obračun: završeni ili oni čiji je termin već prošao.
        $sessionIds = TrainingSession::query()
            ->where('club_id', $clubId)
            ->where(fn ($q) => $q->where('status', 'completed')->orWhere('scheduled_at', '<=', now()))
            ->when($teamId, fn ($q) => $q->where('team_id', $teamId))
            ->when($dateFrom, fn ($q) => $q->whereDate('scheduled_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('scheduled_at', '<=', $dateTo))
            ->pluck('id');

        $morphType = (new TrainingSession())->getMorphClass();

        // Prisustvo po igraču: jedna grupisana agregacija umesto upita po igraču.
        $attendanceByPlayer = $sessionIds->isEmpty()
            ? collect()
            : DB::table('event_invitations')
                ->selectRaw('player_id, COUNT(*) AS invited_count, SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS attended_count', [self::ATTENDED_STATUS])
                ->where('invitable_type', $morphType)
                ->whereIn('invitable_id', $sessionIds)
                ->groupBy('player_id')
                ->get()
                ->keyBy('player_id');

        $players = Player::where('club_id', $clubId)
            ->when($teamId, fn ($q) => $q->where('team_id', $teamId))
            ->with(['team:id,name'])
            ->get()
            ->map(function (Player $player) use ($attendanceByPlayer) {
                $row = $attendanceByPlayer->get($player->id);

                $invitedCount = (int) ($row->invited_count ?? 0);
                $attendedCount = (int) ($row->attended_count ?? 0);

                // Odaziv u procentima u odnosu na broj treninga na koje je igrač pozvan.
                $attendanceRate = $invitedCount > 0
                    ? (int) round(($attendedCount / $invitedCount) * 100)
                    : 0;

                $matchesCount = (int) ($player->matches_played ?? 0);
                $goals = (int) ($player->goals ?? 0);
                $assists = (int) ($player->assists ?? 0);

                // Indeks korisnosti: (Golovi + Asistencije) / Broj odigranih utakmica
                $performanceIndex = $matchesCount > 0
                    ? round(($goals + $assists) / $matchesCount, 2)
                    : 0.0;

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
                    'trainings_invited' => $invitedCount,
                    'trainings_attended' => $attendedCount,
                    'attendance_rate' => $attendanceRate,
                    'performance_index' => $performanceIndex,
                ];
            });

        // Prisustvo po treningu - osnov za prosek odaziva na nivou kluba/tima.
        $sessionAttendance = $sessionIds->isEmpty()
            ? collect()
            : DB::table('event_invitations')
                ->selectRaw('invitable_id, COUNT(*) AS invited_count, SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS attended_count', [self::ATTENDED_STATUS])
                ->where('invitable_type', $morphType)
                ->whereIn('invitable_id', $sessionIds)
                ->groupBy('invitable_id')
                ->get();

        $avgAttendanceRate = $sessionAttendance->isEmpty() ? 0.0 : $sessionAttendance->avg(
            fn ($s) => $s->invited_count > 0 ? ($s->attended_count / $s->invited_count) * 100 : 0
        );

        $avgAttendedPlayers = $sessionAttendance->isEmpty()
            ? 0.0
            : $sessionAttendance->avg(fn ($s) => (int) $s->attended_count);

        $totalMatches = MatchDay::whereIn('team_id', $teams->pluck('id'))
            ->when($teamId, fn ($q) => $q->where('team_id', $teamId))
            ->count();

        $totalGoals = $players->sum('goals');
        $totalAssists = $players->sum('assists');

        return response()->json([
            'teams' => $teams,
            'players' => $players->sortByDesc('performance_index')->values(),
            'summary' => [
                'total_matches' => $totalMatches,
                'total_goals' => $totalGoals,
                'total_assists' => $totalAssists,
                'goals_per_match' => $totalMatches > 0 ? round($totalGoals / $totalMatches, 2) : 0,
                'total_trainings' => $sessionIds->count(),
                'avg_attended_players' => round((float) $avgAttendedPlayers, 1),
                'avg_attendance_rate' => round((float) $avgAttendanceRate, 1),
            ],
        ]);
    }
}
