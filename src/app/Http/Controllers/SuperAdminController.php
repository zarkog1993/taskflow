<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    public function destroyUser(Request $request, User $user): JsonResponse
    {
        $this->authorizeSuperAdmin($request);
        abort_if($request->user()->is($user), 422, 'You cannot delete your own superadmin account.');

        $user->delete();

        return response()->json(null, 204);
    }

    public function destroyClub(Request $request, Club $club): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        DB::transaction(function () use ($club) {
            $club->users()->update(['club_id' => null]);
            $club->delete();
        });

        return response()->json(null, 204);
    }

    public function cancelSubscription(Request $request, Subscription $subscription): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $subscription = $this->setSubscriptionStatus($subscription, 'cancelled');

        return response()->json(['subscription' => $subscription]);
    }

    public function changeSubscriptionPlan(Request $request, Subscription $subscription): JsonResponse
    {
        $this->authorizeSuperAdmin($request);

        $validated = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
        ]);
        $plan = SubscriptionPlan::query()->where('is_active', true)->findOrFail($validated['plan_id']);

        $subscription->update([
            'subscription_plan_id' => $plan->id,
            'plan_type' => $plan->slug,
            'max_teams' => $plan->max_teams,
            'max_players' => $plan->max_players,
            'features' => $plan->features,
            'price' => $plan->price,
        ]);

        return response()->json([
            'subscription' => $subscription->fresh(['plan', 'user.club']),
        ]);
    }

    private function setSubscriptionStatus(Subscription $subscription, string $status): Subscription
    {
        $subscription->update([
            'status' => $status,
            'ends_at' => now(),
        ]);
        $subscription->club()->update(['status' => 'pending_subscription']);

        return $subscription->fresh(['plan', 'user.club']);
    }

    private function authorizeSuperAdmin(Request $request): void
    {
        abort_unless($request->user()->isSuperAdmin(), 403);
    }
}
