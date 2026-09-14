<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TeamController extends Controller
{
    private TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    /**
     * Display a listing of the teams.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $teams = $this->teamService->getAllTeams();
        return response()->json(['data' => $teams]);
    }

    /**
     * Store a new team.
     * @param Request $request
     * @return JsonResponse
     */ 
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age_group' => 'required|string|in:u9,u11,u13,u15,u17,u19,senior',
            'academy_id' => 'nullable|exists:academies,id',
        ]);

        $team = $this->teamService->createTeam($validated);
        return response()->json(['data' => $team], 201);
    }

    /**
     * Assign members to a team.
     * @param Request $request
     * @param Team $team
     * @return JsonResponse
     */ 
    public function assignMembers(Request $request, Team $team): JsonResponse
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $updatedTeam = $this->teamService->assignMembers($team, $validated['user_ids']);
        return response()->json(['data' => $updatedTeam]);
    }
}
