<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Club;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function superAdminDashboard(Request $request): JsonResponse
    {
        // Provera da li je Super Admin
        if (!$request->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'stats' => [
                'total_clubs' => Club::count(),
                'total_users' => User::count(),
                'active_subscriptions' => Subscription::where('status', 'active')->count(),
                'monthly_revenue' => Subscription::where('status', 'active')->sum('price'),
            ],
            'clubs' => Club::with(['owner', 'teams', 'subscription'])->get(),
            'users' => User::with(['club', 'roles'])->latest()->get(),
            'subscriptions' => Subscription::with('user.club')->latest()->get(),
        ]);
    }

    /**
     * Store a newly created user in storage.
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Samo Super Admin i Club Admin mogu da kreiraju korisnike
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'club_id' => 'nullable|exists:clubs,id',
            'role' => 'required|string|exists:roles,slug',
        ]);

        // Kreiranje korisnika
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'club_id' => $validated['club_id'] ?? $request->user()->club_id,
        ]);

        // Dodeljivanje uloge
        $role = Role::where('slug', $validated['role'])->first();
        if ($role) {
            $user->roles()->attach($role->id);
        }

        return response()->json([
            'message' => 'Korisnik uspešno kreiran',
            'data' => $user->load(['club', 'roles'])
        ], 201);
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
