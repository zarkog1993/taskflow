<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use Illuminate\Support\Collection;

class SubscriptionPlanService
{
    public function all(): Collection
    {
        $configuredPlans = config('subscriptions.plans', []);
        $configuredSlugs = array_keys($configuredPlans);

        SubscriptionPlan::query()
            ->whereNotIn('slug', $configuredSlugs)
            ->update(['is_active' => false]);

        return collect($configuredPlans)->map(function (array $plan, string $slug) {
            return SubscriptionPlan::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $plan['name'],
                    'price' => $this->numericPrice($plan['price']),
                    'max_teams' => $plan['max_teams'],
                    'max_players' => $plan['max_players'],
                    'features' => array_values($plan['features']),
                    'is_active' => true,
                ],
            );
        })->values();
    }

    public function find(string $slug): SubscriptionPlan
    {
        return $this->all()->firstWhere('slug', $slug)
            ?? throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(
                'Subscription plan not found.',
            );
    }

    private function numericPrice(int|string $price): int
    {
        return (int) preg_replace('/[^\d]/', '', (string) $price);
    }
}
