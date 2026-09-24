<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Teams
        if (Schema::hasTable('teams') && !Schema::hasColumn('teams', 'club_id')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->foreignId('club_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('clubs')
                    ->cascadeOnDelete();
            });
        }

        // 2. Game Matches
        $matchesTable = Schema::hasTable('game_matches') ? 'game_matches' : (Schema::hasTable('matches') ? 'matches' : null);
        if ($matchesTable && !Schema::hasColumn($matchesTable, 'club_id')) {
            Schema::table($matchesTable, function (Blueprint $table) {
                $table->foreignId('club_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('clubs')
                    ->cascadeOnDelete();
            });
        }

        // 3. Training Sessions
        if (Schema::hasTable('training_sessions') && !Schema::hasColumn('training_sessions', 'club_id')) {
            Schema::table('training_sessions', function (Blueprint $table) {
                $table->foreignId('club_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('clubs')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('teams') && Schema::hasColumn('teams', 'club_id')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->dropForeign(['club_id']);
                $table->dropColumn('club_id');
            });
        }

        $matchesTable = Schema::hasTable('game_matches') ? 'game_matches' : (Schema::hasTable('matches') ? 'matches' : null);
        if ($matchesTable && Schema::hasColumn($matchesTable, 'club_id')) {
            Schema::table($matchesTable, function (Blueprint $table) {
                $table->dropForeign(['club_id']);
                $table->dropColumn('club_id');
            });
        }

        if (Schema::hasTable('training_sessions') && Schema::hasColumn('training_sessions', 'club_id')) {
            Schema::table('training_sessions', function (Blueprint $table) {
                $table->dropForeign(['club_id']);
                $table->dropColumn('club_id');
            });
        }
    }
};
