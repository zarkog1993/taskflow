<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('clubs') && !Schema::hasColumn('clubs', 'status')) {
            Schema::table('clubs', function (Blueprint $table) {
                $table->enum('status', ['pending_onboarding', 'pending_approval', 'approved', 'rejected', 'suspended'])
                    ->default('pending_onboarding');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('clubs') && Schema::hasColumn('clubs', 'status')) {
            Schema::table('clubs', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};