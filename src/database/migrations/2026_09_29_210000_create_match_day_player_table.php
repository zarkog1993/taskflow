<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Zapisnik utakmice se vodi nad tabelom `players` (stvarni sastav ekipe),
     * dok je stari pivot `match_day_user` bio vezan za `users` (nalozi kluba)
     * i zbog toga nikada nije mogao da prikaže igrače.
     */
    public function up(): void
    {
        Schema::create('match_day_player', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_day_id')->constrained('match_days')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->boolean('attended')->default(false);
            $table->unsignedInteger('goals')->default(0);
            $table->unsignedInteger('assists')->default(0);
            $table->timestamps();

            $table->unique(['match_day_id', 'player_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_day_player');
    }
};
