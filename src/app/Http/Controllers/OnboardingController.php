<?php

namespace App\Http\Controllers;

use App\Http\Requests\OnboardingRequest;
use App\Services\OnboardingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        // Ako korisnik već ima klub, odbij ponovno kreiranje
        if ($user->club_id) {
            return response()->json([
                'message' => 'Korisnik već ima registrovan klub.'
            ], 422);
        }

        $club = $this->onboardingService->completeOnboarding($user, $request->validated());

        return response()->json([
            'message' => 'Klub i pretplata su uspešno kreirani!',
            'club' => $club,
        ], 201);
    }
}
