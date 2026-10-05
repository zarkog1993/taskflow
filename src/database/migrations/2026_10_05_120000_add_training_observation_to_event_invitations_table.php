<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_invitations', function (Blueprint $table) {
            $table->text('training_observation')->nullable()->after('responded_at');
        });
    }

    public function down(): void
    {
        Schema::table('event_invitations', function (Blueprint $table) {
            $table->dropColumn('training_observation');
        });
    }
};