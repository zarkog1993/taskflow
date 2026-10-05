<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\OnboardingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    public function __construct(private readonly OnboardingService $onboardingService)
    {
    }

    /**
     * Get a list of available subscription plans.
     * @return JsonResponse
     */
    public function getPlans(): JsonResponse
    {
        return response()->json([
            'plans' => $this->onboardingService->plans(),
        ]);
    }

    /**
     * Show the onboarding details for a specific token.
     * @param string $token
     * @return JsonResponse
     */
    public function show(string $token): JsonResponse
    {
        $club = $this->onboardingService->findByToken($token);

        return response()->json([
            'user' => $club->users->first(),
            'club' => $club,
            'plans' => $this->onboardingService->plans(),
        ]);
    }

    /**
     * Select a subscription plan for the onboarding process.
     * @param Request $request
     * @param string $token
     * @return JsonResponse
     */
    public function select(Request $request, string $token): JsonResponse
    {
        $validated = $request->validate([
            'plan_type' => [
                'required',
                'string',
                Rule::exists('subscription_plans', 'slug')->where('is_active', true),
            ],
        ]);

        $subscription = $this->onboardingService->selectPlan($token, $validated['plan_type']);

        return response()->json([
            'message' => 'Your package selection is pending administrator approval.',
            'subscription' => $subscription,
        ], 202);
    }

    /**
     * Complete the onboarding process and create the club.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_type' => [
                'required',
                'string',
                Rule::exists('subscription_plans', 'slug')->where('is_active', true),
            ],
        ]);

        $club = $this->onboardingService->completeOnboarding($request->user(), $validated);

        return response()->json([
            'message' => 'Your package selection is pending administrator approval.',
            'club' => $club,
        ], 202);
    }

    /**
     * Update the status of a subscription.
     * @param Request $request
     * @param Subscription $subscription
     * @return JsonResponse
     */
    public function updateSubscription(Request $request, Subscription $subscription): JsonResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:approved,active,rejected,cancelled,expired'],
        ]);

        return response()->json([
            'subscription' => $this->onboardingService->approve(
                $subscription,
                $validated['status'],
                $request->user(),
            ),
        ]);
    }
}
