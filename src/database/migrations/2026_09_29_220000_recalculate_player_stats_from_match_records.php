<?php

use App\Services\PlayerStatsService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Jednokratna sinhronizacija: statistika igrača se od sada izvodi iz
     * zapisnika odigranih utakmica, pa postojeće ručno unete vrednosti
     * zamenjujemo stvarnim podacima iz zapisnika.
     */
    public function up(): void
    {
        app(PlayerStatsService::class)->recalculateAll();
    }

    public function down(): void
    {
        // Preračunate vrednosti se ne mogu vratiti na prethodno ručno stanje.
    }
};
