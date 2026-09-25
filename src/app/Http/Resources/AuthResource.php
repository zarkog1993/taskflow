<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this['user']->load(['roles', 'club.teams']);

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => (bool) $user->is_admin,
                'roles' => $user->roles,
                'club_id' => $user->club_id,
                'team_ids' => $user->club?->teams->pluck('id')->values() ?? [],
                'club_status' => $user->club?->status,
                'subscription_status' => $user->subscription?->status,
                'subscription_features' => $user->subscription?->features ?? [],
            ],
            'access_token' => $this['access_token'],
            'token_type' => 'Bearer',
            'onboarding_url' => $this['onboarding_url'] ?? null,
        ];
    }
}
