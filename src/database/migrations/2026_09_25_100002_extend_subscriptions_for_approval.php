<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('club_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->nullable()->after('plan_type')->constrained()->nullOnDelete();
            $table->json('features')->nullable()->after('max_players');
            $table->unsignedInteger('price')->default(0)->after('features');
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
        });

        foreach (DB::table('subscription_plans')->get() as $plan) {
            DB::table('subscriptions')
                ->where('plan_type', $plan->slug)
                ->update([
                    'subscription_plan_id' => $plan->id,
                    'features' => $plan->features,
                    'price' => $plan->price,
                ]);
        }

        DB::statement('
            UPDATE subscriptions
            SET club_id = (SELECT club_id FROM users WHERE users.id = subscriptions.user_id)
            WHERE club_id IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('club_id');
            $table->dropConstrainedForeignId('subscription_plan_id');
            $table->dropColumn(['features', 'approved_by', 'approved_at', 'rejected_at']);
        });
    }
};
