<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->whenLoaded('roles'),
            'teams' => $this->whenLoaded('teams'),

            // Profil igrača i njegovi pojedinačni parametri
            'player_profile' => $this->whenLoaded('playerProfile', function () {
                return [
                    'id' => $this->playerProfile->id,
                    'primary_position' => $this->playerProfile->primary_position,
                    'jersey_number' => $this->playerProfile->jersey_number,
                    'height' => $this->playerProfile->height,
                    'weight' => $this->playerProfile->weight,
                    'seniority' => $this->playerProfile->seniority,
                    'date_of_birth' => $this->playerProfile->date_of_birth,
                    'preferred_foot' => $this->playerProfile->preferred_foot,
                    'physical_status' => $this->playerProfile->physical_status ?? 'fit',
                ];
            }),

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}