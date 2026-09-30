<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Uklanja tabele zaostale iz ranijih faza projekta (task manager + evidencija
 * prisustva vezana za `users`). Aktuelni domen koristi `players`,
 * `event_invitations` i `match_day_player`.
 */
return new class extends Migration
{
    private const LEGACY_TABLES = [
        'comments',
        'tasks',
        'attendance',
        'attendances',
        'training_user',
        'match_day_user',
    ];

    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach (self::LEGACY_TABLES as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Tabele su bile prazne i zamenjene su novim modelom podataka,
        // pa nema smislenog sadržaja za vraćanje.
    }
};
