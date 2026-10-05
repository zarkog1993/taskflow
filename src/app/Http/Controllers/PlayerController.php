<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlayerRequest;
use App\Http\Resources\PlayerResource;
use App\Models\Player;
use App\Models\Subscription;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PlayerController extends Controller
{
    /**
     * Display a listing of the players.
     * @param Request $request
     * @return AnonymousResourceCollection
     */
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
    /**
     * Store a newly created player in storage.
     * @param StorePlayerRequest $request
     * @return JsonResponse
     */
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
        $data['seniority'] = $this->seniorityFromAgeGroup($team->age_group);

        $player = DB::transaction(function () use ($request, $data, $team) {
            if (!$request->user()->isSuperAdmin()) {
                $subscription = Subscription::query()
                    ->where('club_id', $team->club_id)
                    ->lockForUpdate()
                    ->first();

                abort_unless($subscription?->isActive(), 403, 'An active club subscription is required.');
                abort_if(
                    Player::where('club_id', $team->club_id)->count() >= $subscription->max_players,
                    403,
                    'The player limit for this subscription has been reached.',
                );
            }

            if ($request->hasFile('photo')) {
                $data['photo_path'] = $request->file('photo')->store('players', 'public');
            }

            return Player::create($data);
        });

        return response()->json([
            'message' => 'Igrač je uspešno kreiran.',
            'data' => new PlayerResource($player->load('team'))
        ], 201);
    }

    /**
     * Display the specified player.
     * @param Request $request
     * @param Player $player
     * @return JsonResponse
     */
    public function show(Request $request, Player $player): JsonResponse
    {
        $this->authorizePlayer($request, $player);

        return response()->json([
            'data' => new PlayerResource($player->load('team'))
        ]);
    }

    /**
     * Update the specified player in storage.
     * @param Request $request
     * @param Player $player
     * @return JsonResponse
     */
    public function update(Request $request, Player $player): JsonResponse
    {
        $this->authorizePlayer($request, $player);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:500',
            'primary_position' => 'sometimes|string|max:10',
            'jersey_number' => 'nullable|integer',
            'height' => 'nullable|integer',
            'weight' => 'nullable|integer',
            'date_of_birth' => 'nullable|date|before_or_equal:today',
            'preferred_foot' => 'nullable|string|in:right,left,both',
            'physical_status' => 'nullable|string',
            'medical_notes' => 'nullable|string',
            'coach_notes' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',

            // Utakmice, golovi i asistencije se izvode iz zapisnika odigranih
            // utakmica (PlayerStatsService) i ne mogu se menjati ručno.
            'trainings_attended' => 'nullable|integer|min:0',
        ]);

        if (array_key_exists('team_id', $validated) && $validated['team_id'] !== null) {
            $team = $this->resolveAuthorizedTeam($request, $validated['team_id']);
            $dateOfBirth = array_key_exists('date_of_birth', $validated)
                ? $validated['date_of_birth']
                : $player->date_of_birth?->format('Y-m-d');

            if ($dateOfBirth && !$this->isEligibleForAgeGroup($dateOfBirth, $team->age_group)) {
                throw ValidationException::withMessages([
                    'team_id' => 'Igrač ne može biti raspoređen u mlađu kategoriju od one koja odgovara njegovom uzrastu.',
                ]);
            }

            $validated['team_id'] = $team->id;
            $validated['club_id'] = $team->club_id;
            $validated['seniority'] = $this->seniorityFromAgeGroup($team->age_group);
        } else {
            $validated['seniority'] = $this->seniorityFromAgeGroup($player->team?->age_group);
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
    /**
     * Remove the specified player from storage.
     * @param Request $request
     * @param Player $player
     * @return JsonResponse
     */
    public function destroy(Request $request, Player $player): JsonResponse
    {
        $this->authorizePlayer($request, $player);

        if ($player->photo_path) {
            Storage::disk('public')->delete($player->photo_path);
        }
        $player->delete();

        return response()->json(['message' => 'Igrač je uspešno obrisan.']);
    }

    /**
     * Resolve the authorized team for the current user.
     * @param Request $request
     * @param int|null $teamId
     * @return Team
     */
    private function resolveAuthorizedTeam(Request $request, ?int $teamId): Team
    {        $query = Team::query();

        if (!$request->user()->isSuperAdmin()) {
            $query->where('club_id', $request->user()->club_id);
        }

        return $teamId
            ? $query->findOrFail($teamId)
            : $query->oldest('id')->firstOrFail();
    }

    /**
     * Authorize access to a specific player for the current user.
     * @param Request $request
     * @param Player $player
     * @return void
     */
    private function authorizePlayer(Request $request, Player $player): void
    {
        abort_unless(
            $request->user()->isSuperAdmin()
            || (int) $player->club_id === (int) $request->user()->club_id,
            403,
        );
    }

    /**
     * Kategorija igrača se izvodi iz starosne grupe ekipe (u19 -> U19, senior -> Seniori).
     * @param string|null $ageGroup
     * @return string
     */
    private function seniorityFromAgeGroup(?string $ageGroup): string
    {
        if (!$ageGroup || $ageGroup === 'senior') {
            return 'Seniori';
        }

        return strtoupper($ageGroup);
    }

    private function isEligibleForAgeGroup(string $dateOfBirth, string $teamAgeGroup): bool
    {
        $birthDate = new \DateTimeImmutable($dateOfBirth);
        $age = (int) $birthDate->diff(new \DateTimeImmutable('today'))->format('%y');
        $minimumAgeGroup = match (true) {
            $age <= 9 => 'u9',
            $age <= 11 => 'u11',
            $age <= 13 => 'u13',
            $age <= 15 => 'u15',
            $age <= 17 => 'u17',
            $age <= 19 => 'u19',
            default => 'senior',
        };
        $ageGroups = ['u9', 'u11', 'u13', 'u15', 'u17', 'u19', 'senior'];
        $teamIndex = array_search($teamAgeGroup, $ageGroups, true);
        $minimumIndex = array_search($minimumAgeGroup, $ageGroups, true);

        return $teamIndex !== false && $minimumIndex !== false && $teamIndex >= $minimumIndex;
    }
}
