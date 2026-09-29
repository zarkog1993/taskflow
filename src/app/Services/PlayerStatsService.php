<?php

namespace App\Services;

use App\Models\MatchDay;
use App\Models\Player;
use Illuminate\Support\Facades\DB;

/**
 * Statistika igrača (odigrane utakmice, golovi, asistencije) se izvodi isključivo
 * iz zapisnika odigranih utakmica. Kolone u tabeli `players` su keširana
 * vrednost koja se preračunava pri svakoj izmeni zapisnika.
 */
class PlayerStatsService
{
    /**
     * Preračunava statistiku za sve igrače koji su ikada bili u zapisniku
     * date utakmice (uključujući i one koji su upravo uklonjeni iz sastava).
     */
    public function recalculateForMatch(MatchDay $match, array $extraPlayerIds = []): void
    {
        $playerIds = DB::table('match_day_player')
            ->where('match_day_id', $match->id)
            ->pluck('player_id')
            ->merge($extraPlayerIds)
            ->unique()
            ->values()
            ->all();

        $this->recalculateFor($playerIds);
    }

    /**
     * Preračunava i upisuje statistiku za zadate igrače.
     */
    public function recalculateFor(array $playerIds): void
    {
        if (empty($playerIds)) {
            return;
        }

        $aggregates = $this->aggregateQuery()
            ->whereIn('mdp.player_id', $playerIds)
            ->get()
            ->keyBy('player_id');

        foreach ($playerIds as $playerId) {
            $row = $aggregates->get($playerId);

            // Igrač bez ijednog nastupa u odigranoj utakmici se resetuje na nulu.
            Player::withoutGlobalScopes()
                ->whereKey($playerId)
                ->update([
                    'matches_played' => (int) ($row->matches_played ?? 0),
                    'goals' => (int) ($row->goals ?? 0),
                    'assists' => (int) ($row->assists ?? 0),
                ]);
        }
    }

    /**
     * Preračunava statistiku za sve igrače (jednokratna sinhronizacija).
     */
    public function recalculateAll(): void
    {
        $this->recalculateFor(
            Player::withoutGlobalScopes()->pluck('id')->all()
        );
    }

    /**
     * Agregacija nastupa: računaju se samo odigrane (completed) utakmice
     * i samo igrači označeni kao prisutni u sastavu.
     */
    private function aggregateQuery()
    {
        return DB::table('match_day_player as mdp')
            ->join('match_days as md', 'md.id', '=', 'mdp.match_day_id')
            ->where('md.status', 'completed')
            ->where('mdp.attended', true)
            ->groupBy('mdp.player_id')
            ->selectRaw(
                'mdp.player_id,
                 COUNT(*) AS matches_played,
                 COALESCE(SUM(mdp.goals), 0) AS goals,
                 COALESCE(SUM(mdp.assists), 0) AS assists'
            );
    }
}
