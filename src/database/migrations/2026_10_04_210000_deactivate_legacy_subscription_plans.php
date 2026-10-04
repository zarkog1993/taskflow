<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const LEGACY_PLAN_SLUGS = ['pro', 'unlimited'];

    public function up(): void
    {
        DB::table('subscription_plans')
            ->whereIn('slug', self::LEGACY_PLAN_SLUGS)
            ->update(['is_active' => false, 'updated_at' => now()]);
    }

    public function down(): void
    {
        // Retired plans should not be reactivated by rolling back this migration.
    }
};
