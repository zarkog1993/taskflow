<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Usklađuje slugove funkcionalnosti u pretplatama.
 *
 * Paketi `pro` i `unlimited` su zaostali sa starom notacijom sa tačkom
 * (`club.profile`, `advanced.statistics`), dok middleware `subscription.feature`
 * i frontend ruter očekuju notaciju sa donjom crtom. Posledica je bila da klub
 * na paketu `unlimited` — najskupljem — dobija 403 na analitici i profilu kluba.
 *
 * Pored prevođenja slugova, `pro` i `unlimited` se vraćaju na skupove koje
 * ogledaju `standard` odnosno `premium` (uz zadržane dodatke), jer su izvorno
 * bili upravo njihovi ekvivalenti pod drugim imenom.
 */
return new class extends Migration
{
    /**
     * Stara notacija sa tačkom -> kanonska notacija sa donjom crtom.
     */
    private const SLUG_MAP = [
        'club.profile' => 'club_profile',
        'club.basic_information' => 'club_basic_information',
        'advanced.statistics' => 'advanced_stats',
        'advanced.management' => 'advanced_management',
        'additional.content' => 'additional_content',
    ];

    /**
     * Kanonski skup funkcionalnosti po paketu.
     */
    private const PLAN_FEATURES = [
        'basic' => ['club_profile', 'players', 'teams'],
        'standard' => ['club_profile', 'players', 'teams', 'matches', 'news'],
        'premium' => ['club_profile', 'players', 'teams', 'matches', 'news', 'advanced_stats', 'tactics'],
        'pro' => ['club_profile', 'club_basic_information', 'players', 'teams', 'matches', 'news'],
        'unlimited' => [
            'club_profile', 'club_basic_information', 'players', 'teams', 'matches', 'news',
            'advanced_stats', 'tactics', 'advanced_management', 'additional_content',
        ],
    ];

    public function up(): void
    {
        foreach (self::PLAN_FEATURES as $slug => $features) {
            DB::table('subscription_plans')
                ->where('slug', $slug)
                ->update(['features' => json_encode($features), 'updated_at' => now()]);
        }

        // Pretplate nose sopstvenu kopiju funkcionalnosti (`Subscription::hasFeature`
        // čita `subscriptions.features`, ne plan), pa ispravka paketa sama po sebi
        // ne bi odblokirala postojeće korisnike.
        $plans = DB::table('subscription_plans')->pluck('features', 'id');

        foreach (DB::table('subscriptions')->get() as $subscription) {
            $planFeatures = $subscription->subscription_plan_id !== null
                ? ($plans[$subscription->subscription_plan_id] ?? null)
                : null;

            $features = $planFeatures !== null
                ? json_decode($planFeatures, true)
                : $this->translate(json_decode($subscription->features ?? '[]', true));

            DB::table('subscriptions')
                ->where('id', $subscription->id)
                ->update(['features' => json_encode(array_values($features)), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Stari slugovi su bili neispravni i blokirali su plaćene korisnike,
        // pa nema smisla vraćati ih.
    }

    /**
     * Prevodi listu slugova iz notacije sa tačkom u kanonsku notaciju.
     */
    private function translate(array $features): array
    {
        $translated = array_map(
            fn (string $feature) => self::SLUG_MAP[$feature] ?? $feature,
            $features
        );

        return array_values(array_unique($translated));
    }
};
