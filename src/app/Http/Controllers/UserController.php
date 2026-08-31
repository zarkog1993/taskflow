<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function index()
    {
        $users = $this->userService->getAllPaginated();

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request): UserResource
    {
        $data = $request->validated();
        $user = $this->userService->store($data);
        return new UserResource($user);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        Gate::authorize('update', $user);

        $updatedUser = $this->userService->update($user, $request->validated());

        return new UserResource($updatedUser);
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        $this->userService->delete($user);

        return response()->noContent();
    }

    public function updateRoles(Request $request, User $user): UserResource
    {
        Gate::authorize('update', $user); // ili tvoja polisa za izmenu uloga

        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        $updatedUser = $this->userService->updateRoles($user, $request->input('roles', []));

        return new UserResource($updatedUser);
    }
}
