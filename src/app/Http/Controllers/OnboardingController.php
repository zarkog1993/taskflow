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

    public function getPlans(): JsonResponse
    {
        return response()->json([
            'plans' => $this->onboardingService->plans(),
        ]);
    }

    public function show(string $token): JsonResponse
    {
        $club = $this->onboardingService->findByToken($token);

        return response()->json([
            'user' => $club->users->first(),
            'club' => $club,
            'plans' => $this->onboardingService->plans(),
        ]);
    }

    public function select(Request $request, string $token): JsonResponse
    {
        $validated = $request->validate([
            'plan_type' => ['required', 'string', Rule::in(array_keys(config('subscriptions.plans', [])))],
        ]);

        $subscription = $this->onboardingService->selectPlan($token, $validated['plan_type']);

        return response()->json([
            'message' => 'Your package selection is pending administrator approval.',
            'subscription' => $subscription,
        ], 202);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'plan_type' => ['required', 'string', Rule::in(array_keys(config('subscriptions.plans', [])))],
        ]);

        $club = $this->onboardingService->completeOnboarding($request->user(), $validated);

        return response()->json([
            'message' => 'Your package selection is pending administrator approval.',
            'club' => $club,
        ], 202);
    }

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
