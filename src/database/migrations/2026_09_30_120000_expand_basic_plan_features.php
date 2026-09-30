<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Proširuje `basic` paket na pregled igrača i timova.
 *
 * Do sada je `basic` imao samo `club_profile`, pa je korisnik posle kupovine
 * video praktično praznu aplikaciju. Utakmice (`matches`) ostaju namerno
 * izvan paketa i predstavljaju razliku u odnosu na `standard`.
 */
return new class extends Migration
{
    private const BASIC_FEATURES = ['club_profile', 'players', 'teams'];

    public function up(): void
    {
        $this->sync(self::BASIC_FEATURES);
    }

    public function down(): void
    {
        $this->sync(['club_profile']);
    }

    /**
     * Upisuje skup funkcionalnosti u `basic` paket i u sve pretplate
     * vezane za njega. Pretplate nose sopstvenu kopiju liste
     * (`Subscription::hasFeature` čita `subscriptions.features`), pa se
     * izmena paketa ne bi odrazila na postojeće korisnike bez ovog koraka.
     */
    private function sync(array $features): void
    {
        $planId = DB::table('subscription_plans')->where('slug', 'basic')->value('id');

        if ($planId === null) {
            return;
        }

        $encoded = json_encode($features);

        DB::table('subscription_plans')
            ->where('id', $planId)
            ->update(['features' => $encoded, 'updated_at' => now()]);

        DB::table('subscriptions')
            ->where('subscription_plan_id', $planId)
            ->update(['features' => $encoded, 'updated_at' => now()]);
    }
};
