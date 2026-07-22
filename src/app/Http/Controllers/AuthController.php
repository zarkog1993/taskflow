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
    public function register(RegisterRequest $registerRequest): JsonResponse {
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

    public function me(Request $request): AuthResource
    {
        // Vraćamo korisnika kroz naš AuthResource, ali bez tokena ovoga puta
        // Možemo proslediti strukturu sličnu servisu
        return new AuthResource([
            'user' => $request->user(),
            'access_token' => null, // ili možemo prilagoditi resurs, ali za /me nam token ne treba ponovo
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
