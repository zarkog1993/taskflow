<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'club_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('club_id')->nullable()->constrained('clubs')->nullOnDelete();
            });
        }

        if (Schema::hasTable('teams') && !Schema::hasColumn('teams', 'club_id')) {
            Schema::table('teams', function (Blueprint $table) {
                $table->foreignId('club_id')->nullable()->constrained('clubs')->cascadeOnDelete();
            });
        }

        if (Schema::hasTable('players') && !Schema::hasColumn('players', 'club_id')) {
            Schema::table('players', function (Blueprint $table) {
                $table->foreignId('club_id')->nullable()->constrained('clubs')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
            $table->dropColumn('club_id');
        });
    }
};