<?php

namespace App\Services;

use App\Models\SubscriptionPlan;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubscriptionPlanService
{
    public function all(): Collection
    {
        $configuredOrder = array_flip(array_keys(config('subscriptions.plans', [])));

        return SubscriptionPlan::query()
            ->where('is_active', true)
            ->get()
            ->sortBy(fn (SubscriptionPlan $plan) => [
                isset($configuredOrder[$plan->slug]) ? 0 : 1,
                $configuredOrder[$plan->slug] ?? $plan->slug,
            ])
            ->values();
    }

    public function findActive(string $slug): SubscriptionPlan
    {
        return SubscriptionPlan::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first()
            ?? throw new NotFoundHttpException('Active subscription plan not found.');
    }

    public function findBySlug(string $slug): SubscriptionPlan
    {
        return SubscriptionPlan::query()->where('slug', $slug)->first()
            ?? throw new NotFoundHttpException('Subscription plan not found.');
    }
}
