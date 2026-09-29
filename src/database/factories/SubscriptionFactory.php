<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        // Pronalazimo ili kreiramo 'basic' plan iz tvoje migracije
        $plan = SubscriptionPlan::where('slug', 'basic')->first()
            ?? SubscriptionPlan::factory()->create();

        return [
            'club_id' => Club::factory(),
            'user_id' => User::factory(),
            'subscription_plan_id' => $plan->id, // Pozivamo ID plana umesto 'type'
            'plan_type' => $plan->slug,
            'ends_at' => now()->addYear(),
            'status' => 'active',
        ];
    }
}