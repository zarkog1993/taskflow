<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate; // <-- 1. DODAT IMPORT

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
    public function index(Request $request)
    {
        $user = $request->user();

        // Ako je Super Admin (is_admin = 1), može videti sve timove
        if ($user->isSuperAdmin()) {
            $teams = Team::with('players')->get();
            return response()->json(['data' => $teams]);
        }

        // Za obične klupske admine / igrače, filtriramo striktno po njihovom klubu
        // Napomena: proveri da li ti se polje u tabeli zove club_id ili academy_id
        $clubId = $user->club_id ?? $user->academy_id;

        if (!$clubId) {
            // Ako korisnik još uvek nema dodeljen klub, vraćamo prazan niz timova
            return response()->json(['data' => []]);
        }

        $teams = Team::where('club_id', $clubId) // ili 'academy_id', zavisno od tvoje migracije
        ->with('players')
            ->get();

        return response()->json(['data' => $teams]);
    }

    public function show(Request $request, Team $team): JsonResponse
    {
        Gate::authorize('view', $team);

        return response()->json([
            'data' => $team->load(['club', 'players']),
        ]);
    }

    /**
     * Store a new team.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // 2. POZIV POLICY PROVERE (Vraća 403 ako je limit dostignut)
        Gate::authorize('create', Team::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'age_group' => 'required|string|in:u9,u11,u13,u15,u17,u19,senior',
            'academy_id' => 'nullable|exists:academies,id',
            'club_id' => 'nullable|exists:clubs,id',
        ]);

        // Automatski dodeljujemo club_id ulogovanog korisnika ako nije prosleđen
        if ($request->user() && $request->user()->club_id) {
            $validated['club_id'] = $request->user()->club_id;
        }

        $team = $this->teamService->createTeam($validated);
        return response()->json(['data' => $team], 201);
    }

    public function update(Request $request, Team $team): JsonResponse
    {
        Gate::authorize('update', $team);

        $team->update($request->validate([
            'name' => 'sometimes|required|string|max:255',
            'age_group' => 'sometimes|required|string|in:u9,u11,u13,u15,u17,u19,senior',
        ]));

        return response()->json(['data' => $team->fresh(['club', 'players'])]);
    }

    public function destroy(Team $team): JsonResponse
    {
        Gate::authorize('delete', $team);
        $team->delete();

        return response()->json(null, 204);
    }

    /**
     * Assign members to a team.
     * @param Request $request
     * @param Team $team
     * @return JsonResponse
     */
    public function assignMembers(Request $request, Team $team): JsonResponse
    {
        Gate::authorize('update', $team);

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => [
                'integer',
                \Illuminate\Validation\Rule::exists('users', 'id')->where('club_id', $team->club_id),
            ],
        ]);

        $updatedTeam = $this->teamService->assignMembers($team, $validated['user_ids']);
        return response()->json(['data' => $updatedTeam]);
    }
}
