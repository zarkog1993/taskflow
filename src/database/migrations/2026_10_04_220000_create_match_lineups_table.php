<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_lineups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_day_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('formation', 10);
            $table->json('positions');
            $table->json('bench_player_ids');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_lineups');
    }
};
