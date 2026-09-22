<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'photo_url' => $this->photo_url,
            'primary_position' => $this->primary_position,
            'seniority' => $this->seniority,
            'jersey_number' => $this->jersey_number,
            'height' => $this->height,
            'weight' => $this->weight,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'preferred_foot' => $this->preferred_foot,
            'physical_status' => $this->physical_status,
            'medical_notes' => $this->medical_notes,
            'coach_notes' => $this->coach_notes,
            'stats' => [
                'matches_played' => $this->matches_played,
                'trainings_attended' => $this->trainings_attended,
                'goals' => $this->goals,
                'assists' => $this->assists,
            ],
            'team' => $this->whenLoaded('team', function () {
                return [
                    'id' => $this->team->id,
                    'name' => $this->team->name,
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
