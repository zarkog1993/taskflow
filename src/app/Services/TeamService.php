<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class TeamService
{
    public function getAllTeams(): Collection
    {
        return Team::with(['members' => function ($q) {
            $q->with('playerProfile');
        }])->latest()->get();
    }

    public function createTeam(array $data): Team
    {
        return Team::create([
            'name' => $data['name'],
            'age_group' => $data['age_group'],
            'academy_id' => $data['academy_id'] ?? null,
        ]);
    }

    public function assignMembers(Team $team, array $userIds): Team
    {
        $team->members()->sync($userIds);
        return $team->load('members.playerProfile');
    }
}
