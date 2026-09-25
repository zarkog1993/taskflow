<?php

namespace App\Http\Controllers;

use App\Http\Requests\OnboardingRequest;
use App\Services\OnboardingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Subscription;

class OnboardingController extends Controller
{
    protected OnboardingService $onboardingService;

    public function __construct(OnboardingService $onboardingService)
    {
        $this->onboardingService = $onboardingService;
    }

    /**
     * Pokretanje/Završavanje onboarding procesa (kreiranje kluba i pretplate).
     */
    public function store(OnboardingRequest $request): JsonResponse
    {
        $user = $request->user();

        $club = $this->onboardingService->completeOnboarding($user, $request->validated());

        return response()->json([
            'message' => 'Paket je poslat na odobrenje.',
            'club' => $club,
            'subscription' => $club->subscription,
        ], 202);
    }

    public function plans(): JsonResponse
    {
        return response()->json(['data' => $this->onboardingService->plans()]);
    }

    public function show(string $token): JsonResponse
    {
        $club = $this->onboardingService->findByToken($token);

        return response()->json([
            'club' => $club->only(['name', 'address', 'city', 'country', 'phone', 'status']),
            'plans' => $this->onboardingService->plans(),
        ]);
    }

    public function select(string $token, Request $request): JsonResponse
    {
        $planType = $request->validate([
            'plan_type' => ['required', 'string', 'exists:subscription_plans,slug'],
        ])['plan_type'];
        $subscription = $this->onboardingService->selectPlan($token, $planType);

        return response()->json([
            'message' => 'Paket je poslat na odobrenje.',
            'subscription' => $subscription,
        ], 202);
    }

    public function updateSubscription(Request $request, Subscription $subscription): JsonResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403);
        $validated = $request->validate(['status' => 'required|string']);

        return response()->json([
            'subscription' => $this->onboardingService->approve(
                $subscription,
                $validated['status'],
                $request->user(),
            ),
        ]);
    }
}
