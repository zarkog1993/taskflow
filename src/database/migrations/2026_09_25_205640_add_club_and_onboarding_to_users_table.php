<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'club_id')) {
                $table->foreignId('club_id')->nullable()->after('password')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'onboarding_token')) {
                $table->string('onboarding_token', 64)->nullable()->unique()->after('club_id');
                $table->timestamp('onboarding_token_expires_at')->nullable()->after('onboarding_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
            $table->dropColumn(['club_id', 'onboarding_token', 'onboarding_token_expires_at']);
        });
    }
};