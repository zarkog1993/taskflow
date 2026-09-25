<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlayerRequest;
use App\Http\Resources\PlayerResource;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class PlayerController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $players = Player::with('team')
            ->when(
                !$request->user()->isSuperAdmin(),
                fn ($query) => $query->where('club_id', $request->user()->club_id),
            )
            ->latest()
            ->get();
        return PlayerResource::collection($players);
    }

    public function store(StorePlayerRequest $request): JsonResponse
    {
        abort_unless(
            $request->user()->isSuperAdmin() || $request->user()->isClubAdmin(),
            403,
        );

        $data = $request->validated();
        $team = $this->resolveAuthorizedTeam($request, $data['team_id'] ?? null);
        $data['team_id'] = $team->id;
        $data['club_id'] = $team->club_id;

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('players', 'public');
        }

        $player = Player::create($data);

        return response()->json([
            'message' => 'Igrač je uspešno kreiran.',
            'data' => new PlayerResource($player->load('team'))
        ], 201);
    }

    public function show(Request $request, Player $player): JsonResponse
    {
        $this->authorizePlayer($request, $player);

        return response()->json([
            'data' => new PlayerResource($player->load('team'))
        ]);
    }

    public function update(Request $request, Player $player): JsonResponse
    {
        $this->authorizePlayer($request, $player);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:500',
            'primary_position' => 'sometimes|string|max:10',
            'seniority' => 'nullable|string|max:50',
            'jersey_number' => 'nullable|integer',
            'height' => 'nullable|integer',
            'weight' => 'nullable|integer',
            'date_of_birth' => 'nullable|date',
            'preferred_foot' => 'nullable|string|in:right,left,both',
            'physical_status' => 'nullable|string',
            'medical_notes' => 'nullable|string',
            'coach_notes' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',

            // STATISTIČKA POLJA (Obavezno za izmenu učinka)
            'matches_played' => 'nullable|integer|min:0',
            'trainings_attended' => 'nullable|integer|min:0',
            'goals' => 'nullable|integer|min:0',
            'assists' => 'nullable|integer|min:0',
        ]);

        if (array_key_exists('team_id', $validated) && $validated['team_id'] !== null) {
            $team = $this->resolveAuthorizedTeam($request, $validated['team_id']);
            $validated['team_id'] = $team->id;
            $validated['club_id'] = $team->club_id;
        }

        if ($request->hasFile('photo')) {
            if ($player->photo_path) {
                Storage::disk('public')->delete($player->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('players', 'public');
        }

        $player->update($validated);

        return response()->json([
            'message' => 'Igrač je uspešno ažuriran.',
            'data' => new PlayerResource($player->load('team'))
        ]);
    }

    public function destroy(Request $request, Player $player): JsonResponse
    {
        $this->authorizePlayer($request, $player);

        if ($player->photo_path) {
            Storage::disk('public')->delete($player->photo_path);
        }
        $player->delete();

        return response()->json(['message' => 'Igrač je uspešno obrisan.']);
    }

    private function resolveAuthorizedTeam(Request $request, ?int $teamId): Team
    {
        $query = Team::query();

        if (!$request->user()->isSuperAdmin()) {
            $query->where('club_id', $request->user()->club_id);
        }

        return $teamId
            ? $query->findOrFail($teamId)
            : $query->oldest('id')->firstOrFail();
    }

    private function authorizePlayer(Request $request, Player $player): void
    {
        abort_unless(
            $request->user()->isSuperAdmin()
            || (int) $player->club_id === (int) $request->user()->club_id,
            403,
        );
    }
}