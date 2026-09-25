<?php

namespace App\Services;

use App\Models\Club;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Mail\SubscriptionPendingMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OnboardingService
{
    public function plans()
    {
        return SubscriptionPlan::query()
            ->where('is_active', true)
            ->orderBy('price')
            ->get();
    }

    public function findByToken(string $token): Club
    {
        $club = Club::with('users')->where('onboarding_token_hash', hash('sha256', $token))->first();

        if (!$club || !$club->onboarding_token_expires_at || $club->onboarding_token_expires_at->isPast()) {
            throw new HttpException(410, 'Onboarding link is invalid or has expired.');
        }

        return $club;
    }

    public function selectPlan(string $token, string $planType): Subscription
    {
        return DB::transaction(function () use ($token, $planType) {
            $club = $this->findByToken($token);
            $owner = $club->users()->oldest('users.id')->firstOrFail();
            $plan = SubscriptionPlan::where('slug', $planType)->where('is_active', true)->firstOrFail();

            $subscription = Subscription::updateOrCreate(
                ['user_id' => $owner->id],
                [
                    'club_id' => $club->id,
                    'subscription_plan_id' => $plan->id,
                    'plan_type' => $plan->slug,
                    'status' => 'pending',
                    'max_teams' => $plan->max_teams,
                    'max_players' => $plan->max_players,
                    'features' => $plan->features,
                    'price' => $plan->price,
                    'ends_at' => null,
                ],
            );

            $club->update([
                'status' => 'pending_subscription',
                'onboarding_token_hash' => null,
                'onboarding_token_expires_at' => null,
            ]);

            $subscription = $subscription->load(['plan', 'user', 'club']);

            User::query()
                ->where('is_admin', true)
                ->orWhereHas('roles', fn ($query) => $query->whereIn('slug', ['admin', 'super-admin']))
                ->get()
                ->each(fn (User $superAdmin) => Mail::to($superAdmin->email)->send(
                    new SubscriptionPendingMail($subscription)
                ));

            return $subscription;
        });
    }

    public function completeOnboarding(User $user, array $data): Club
    {
        $club = $user->club;
        if (!$club) {
            throw new HttpException(422, 'No pending club registration found.');
        }

        $subscription = $this->selectPlanForUser($user, $data['plan_type']);
        return $club->fresh()->load('users', 'subscription');
    }

    private function selectPlanForUser(User $user, string $planType): Subscription
    {
        $plan = SubscriptionPlan::where('slug', $planType)->where('is_active', true)->firstOrFail();

        return Subscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'club_id' => $user->club_id,
                'subscription_plan_id' => $plan->id,
                'plan_type' => $plan->slug,
                'status' => 'pending',
                'max_teams' => $plan->max_teams,
                'max_players' => $plan->max_players,
                'features' => $plan->features,
                'price' => $plan->price,
                'ends_at' => null,
            ],
        );
    }

    public function approve(Subscription $subscription, string $status, User $approver): Subscription
    {
        if (!in_array($status, ['approved', 'active', 'rejected', 'cancelled', 'expired'], true)) {
            throw new HttpException(422, 'Invalid subscription status.');
        }

        return DB::transaction(function () use ($subscription, $status, $approver) {
            $subscription->update([
                'status' => $status,
                'approved_by' => in_array($status, ['approved', 'active'], true) ? $approver->id : null,
                'approved_at' => in_array($status, ['approved', 'active'], true) ? now() : null,
                'rejected_at' => $status === 'rejected' ? now() : null,
                'ends_at' => in_array($status, ['approved', 'active'], true) ? now()->addMonth() : null,
            ]);

            $subscription->club()->update([
                'status' => in_array($status, ['approved', 'active'], true) ? 'active' : 'pending_subscription',
                'onboarding_completed_at' => in_array($status, ['approved', 'active'], true) ? now() : null,
            ]);

            return $subscription->fresh('plan');
        });
    }
}
