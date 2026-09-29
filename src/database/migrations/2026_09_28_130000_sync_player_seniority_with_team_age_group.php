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
            ->whereNotNull('team_id')
            ->update([
                'seniority' => DB::raw("
                    (SELECT CASE 
                        WHEN age_group = 'senior' THEN 'Seniori' 
                        ELSE UPPER(age_group) 
                    END 
                    FROM teams 
                    WHERE teams.id = players.team_id)
                ")
            ]);
    }

    public function down(): void
    {
        // Nema povratka: prethodne vrednosti nisu bile tačne.
    }
};
