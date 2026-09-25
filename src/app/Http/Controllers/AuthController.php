<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\AuthResource;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user.
     *
     * @param RegisterRequest $registerRequest
     * @return JsonResponse
     */
    public function register(RegisterRequest $registerRequest): JsonResponse
    {
        $data = $registerRequest->validated();
        $result = $this->authService->register($data);

        return (new AuthResource($result))->response()->setStatusCode(201);
    }

    /**
     * Login a user.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $result = $this->authService->login($credentials);

        return (new AuthResource($result))
            ->response()
            ->setStatusCode(200); // Za login vraćamo 200 OK
    }

    public function me(Request $request)
    {
        $user = $request->user()->load(['roles', 'club.teams']);

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_admin' => (bool) $user->is_admin,
            'club_id' => $user->club_id ?? $user->academy_id,
            'team_ids' => $user->club?->teams->pluck('id')->values() ?? [],
            'roles' => $user->roles,
            'club_status' => $user->club?->status,
            'subscription_status' => $user->subscription?->status,
            'subscription_features' => $user->subscription?->features ?? [],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Uspešno ste se odjavili.'
        ], 200);
    }
}
