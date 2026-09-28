<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Usklađuje postojeće igrače sa starosnom grupom njihove ekipe,
     * jer je `seniority` ranije uvek ostajao na podrazumevanoj vrednosti 'Seniori'.
     */
    public function up(): void
    {
        DB::table('players')
            ->join('teams', 'players.team_id', '=', 'teams.id')
            ->update([
                'players.seniority' => DB::raw("CASE WHEN teams.age_group = 'senior' THEN 'Seniori' ELSE UPPER(teams.age_group) END"),
            ]);
    }

    public function down(): void
    {
        // Nema povratka: prethodne vrednosti nisu bile tačne.
    }
};
