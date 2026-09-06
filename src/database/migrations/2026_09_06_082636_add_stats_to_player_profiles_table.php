<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('player_profiles', function (Blueprint $table) {
            $table->integer('matches_played')->default(0)->after('fitness_status');
            $table->integer('trainings_attended')->default(0)->after('matches_played');
            $table->integer('goals')->default(0)->after('trainings_attended');
            $table->integer('assists')->default(0)->after('goals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_profiles', function (Blueprint $table) {
            $table->dropColumn(['matches_played', 'trainings_attended', 'goals', 'assists']);
        });
    }
};
