<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('name');
            $table->string('address')->nullable()->after('logo_url');
            $table->string('city')->nullable()->after('address');
            $table->string('country')->nullable()->after('city');
            $table->string('phone')->nullable()->after('country');
            $table->string('onboarding_token_hash')->nullable()->unique()->after('phone');
            $table->timestamp('onboarding_token_expires_at')->nullable()->after('onboarding_token_hash');
            $table->timestamp('onboarding_completed_at')->nullable()->after('onboarding_token_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'address',
                'city',
                'country',
                'phone',
                'onboarding_token_hash',
                'onboarding_token_expires_at',
                'onboarding_completed_at',
            ]);
        });
    }
};
