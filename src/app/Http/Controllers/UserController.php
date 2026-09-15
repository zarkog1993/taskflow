<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

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
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $users = $this->userService->getAllPaginated();

        return UserResource::collection($users);
    }

    /**
     * Store a newly created user in storage.
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->store($request->validated());

        return response()->json([
            'data' => new UserResource($user)
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
     * Delete a user.
     * @param User $user
     */
    public function destroy(User $user): JsonResponse
    {
        // Briše igrača (zbog cascade podešavanja obrisaće se i player_profile)
        $user->delete();

        return response()->json([
            'message' => 'Igrač je uspešno obrisan.'
        ]);
    }

    /**
     * Update roles of a user.
     * @param Request $request
     * @param User $user
     * @return UserResource
     */
    public function updateRoles(Request $request, User $user): UserResource
    {
        Gate::authorize('update', $user);

        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        $updatedUser = $this->userService->updateRoles($user, $request->input('roles', []));

        return new UserResource($updatedUser);
    }

    /**
     * Update the statistics of a user's player profile.
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function updateStats(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'matches_played' => 'required|integer|min:0',
            'trainings_attended' => 'required|integer|min:0',
            'goals' => 'required|integer|min:0',
            'assists' => 'required|integer|min:0',
            'category' => 'nullable|string',
            'seniority' => 'nullable|string',
        ]);

        $profile = $user->playerProfile()->firstOrCreate(['user_id' => $user->id]);
        $profile->update($validated);

        return response()->json([
            'data' => $user->load(['roles', 'playerProfile'])
        ]);
    }

    public function updateProfile(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'jersey_number' => 'nullable|string|max:10',
            'primary_position' => 'nullable|string|max:10',
            'preferred_foot' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'fitness_status' => 'nullable|string|max:255',
            'medical_notes' => 'nullable|string',
            'photo_url' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:50',
        ]);

        if (isset($validated['name'])) {
            $user->update(['name' => $validated['name']]);
        }

        $user->playerProfile()->updateOrCreate(
            ['user_id' => $user->id],
            array_diff_key($validated, ['name' => ''])
        );

        return response()->json([
            'message' => 'Profil uspešno ažuriran.',
            'data' => $user->load(['roles', 'playerProfile', 'teams'])
        ]);
    }
}
