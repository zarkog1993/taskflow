<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class TeamService
{
    public function getAllTeams(): Collection
    {
        $user = auth()->user();

        // Super Admin vidi SVE timove u bazi bez obzira na članstvo
        if ($user && $user->hasRole('admin')) {
            return Team::withoutGlobalScope('academy')
                ->with(['members.playerProfile'])
                ->latest()
                ->get();
        }

        // Obični Team Admin vidi samo tim u kome je član
        return Team::whereHas('members', function ($q) use ($user) {$q->where('users.id', $user->id);})
            ->with(['members.playerProfile'])
            ->latest()
            ->get();
    }

    public function createTeam(array $data): Team
    {
        $team = Team::create([
            'name' => $data['name'],
            'age_group' => $data['age_group'],
            'academy_id' => $data['academy_id'] ?? null,
        ]);

        // Automatski dodajemo kreatora (Team Admin-a) u članove tima
        if (auth()->check()) {
            $team->members()->attach(auth()->id());
        }

        return $team;
    }

    public function assignMembers(Team $team, array $userIds): Team
    {
        // Ne izbacujemo samog Team Admina iz tima prilikom osvežavanja sastava
        if (!in_array(auth()->id(), $userIds) && !auth()->user()->hasRole('admin')) {
            $userIds[] = auth()->id();
        }

        $team->members()->sync($userIds);
        return $team->load('members.playerProfile');
    }
}
