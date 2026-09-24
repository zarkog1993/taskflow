<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Club;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * @var UserService $userService
     */
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        return response()->json(
            $this->userService->getPaginatedUsers($request->user())
        );
    }

    /**
     * Store a newly created user in storage.
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request)
    {
        Gate::authorize('create', User::class);

        $user = $this->userService->store($request->validated(), $request->user());

        return response()->json($user, 201);
    }

        /**
     * Display the specified user.
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'data' => $user->load(['roles', 'playerProfile', 'teams'])
        ]);
    }

    /**
     * Update the specified user in storage.
     * @param UpdateUserRequest $request
     * @param User $user
     * @return UserResource
     */
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        Gate::authorize('update', $user);

        $updatedUser = $this->userService->update($user, $request->validated());

        return new UserResource($updatedUser);
    }

    /**
     * Uklanja korisnika iz baze.
     */
    public function destroy(Request $request, User $user)
    {
        Gate::authorize('delete', $user);

        $this->userService->delete($user);

        return response()->json(null, 204);
    }

    public function adminClubsOverview(Request $request): JsonResponse
    {
        if (!$request->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Učitavamo klubove sa vlasnikom, timovima i igračima
        $clubs = Club::with(['owner', 'teams.players', 'subscription'])->get();

        return response()->json(['data' => $clubs]);
    }
}
