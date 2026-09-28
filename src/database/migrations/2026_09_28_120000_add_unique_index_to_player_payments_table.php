<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('player_payments', function (Blueprint $table) {
            $table->unique(
                ['team_id', 'player_id', 'type', 'period'],
                'player_payments_unique_entry',
            );
        });
    }

    public function down(): void
    {
        Schema::table('player_payments', function (Blueprint $table) {
            $table->dropUnique('player_payments_unique_entry');
        });
    }
};
