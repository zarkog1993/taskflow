<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('training_sessions', 'club_id')) {
            Schema::table('training_sessions', function (Blueprint $table) {
                $table->foreignId('club_id')
                    ->nullable()
                    ->after('team_id')
                    ->constrained('clubs')
                    ->cascadeOnDelete();
            });
        }

        DB::table('training_sessions')
            ->join('teams', 'teams.id', '=', 'training_sessions.team_id')
            ->whereNull('training_sessions.club_id')
            ->whereNotNull('teams.club_id')
            ->select('training_sessions.id', 'teams.club_id')
            ->orderBy('training_sessions.id')
            ->each(function ($session) {
                DB::table('training_sessions')
                    ->where('id', $session->id)
                    ->update(['club_id' => $session->club_id]);
            });

        DB::table('training_sessions')
            ->join('users', 'users.id', '=', 'training_sessions.created_by')
            ->whereNull('training_sessions.club_id')
            ->whereNotNull('users.club_id')
            ->select('training_sessions.id', 'users.club_id')
            ->orderBy('training_sessions.id')
            ->each(function ($session) {
                DB::table('training_sessions')
                    ->where('id', $session->id)
                    ->update(['club_id' => $session->club_id]);
            });
    }

    public function down(): void
    {
        // Do not remove a tenant ownership column that may predate this repair migration.
    }
};
