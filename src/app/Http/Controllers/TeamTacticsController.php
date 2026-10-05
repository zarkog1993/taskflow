<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TeamTacticsController extends Controller
{
    private const array FORMATIONS = ['4-3-3', '4-4-2', '4-2-3-1', '3-5-2'];

    /**
     * Display the tactics of the specified team.
     * @param Team $team
     * @return JsonResponse
     */
    public function show(Team $team): JsonResponse
    {
        Gate::authorize('view', $team);

        return response()->json([
            'data' => $team->tactic,
        ]);
    }

    /**
     * Update the tactics of the specified team.
     * @param Request $request
     * @param Team $team
     * @return JsonResponse
     */
    public function update(Request $request, Team $team): JsonResponse
    {
        Gate::authorize('update', $team);

        $validated = $request->validate([
            'formation' => ['required', 'string', Rule::in(self::FORMATIONS)],
            'positions' => ['required', 'array', 'size:11'],
            'positions.*' => ['required', 'array:spot_id,x,y,player_id'],
            'positions.*.spot_id' => ['required', 'integer', 'between:1,11', 'distinct:strict'],
            'positions.*.x' => ['required', 'numeric', 'between:0,100'],
            'positions.*.y' => ['required', 'numeric', 'between:0,100'],
            'positions.*.player_id' => [
                'present',
                'nullable',
                'integer',
                Rule::exists('players', 'id')->where('team_id', $team->id),
            ],
        ]);

        $playerIds = collect($validated['positions'])
            ->pluck('player_id')
            ->filter(fn ($playerId) => $playerId !== null)
            ->map(fn ($playerId) => (int) $playerId);

        if ($playerIds->duplicates()->isNotEmpty()) {
            throw ValidationException::withMessages([
                'positions' => 'Igrač može biti dodeljen samo na jednu poziciju.',
            ]);
        }

        $tactic = $team->tactic()->updateOrCreate([], $validated);

        return response()->json([
            'message' => 'Taktika je uspešno sačuvana.',
            'data' => $tactic,
        ]);
    }
}
