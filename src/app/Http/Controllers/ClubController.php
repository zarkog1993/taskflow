<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->club]);
    }

    public function update(Request $request): JsonResponse
    {
        $club = $request->user()->club;
        abort_unless($club, 404);

        $club->update($request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'logo_url' => ['nullable', 'url', 'max:2048'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]));

        return response()->json(['data' => $club->fresh()]);
    }
}
