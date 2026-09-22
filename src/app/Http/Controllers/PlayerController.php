<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePlayerRequest;
use App\Http\Resources\PlayerResource;
use App\Models\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class PlayerController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $players = Player::with('team')->latest()->get();
        return PlayerResource::collection($players);
    }

    public function store(StorePlayerRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('players', 'public');
        }

        $player = Player::create($data);

        return response()->json([
            'message' => 'Igrač je uspešno kreiran.',
            'data' => new PlayerResource($player->load('team'))
        ], 201);
    }

    public function show(Player $player): JsonResponse
    {
        return response()->json([
            'data' => new PlayerResource($player->load('team'))
        ]);
    }

    public function update(Request $request, Player $player): JsonResponse
    {
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

    public function destroy(Player $player): JsonResponse
    {
        if ($player->photo_path) {
            Storage::disk('public')->delete($player->photo_path);
        }
        $player->delete();

        return response()->json(['message' => 'Igrač je uspešno obrisan.']);
    }
}