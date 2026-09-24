<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function viewAny(User $authUser): bool
    {
        return true; // Svi ulogovani korisnici mogu dohvatiti listu (filtriranu kroz servis)
    }

    public function view(User $authUser, Team $team): bool
    {
        if ($authUser->isSuperAdmin()) {
            return true;
        }

        return (int) $authUser->club_id === (int) $team->club_id;
    }

    public function create(User $authUser): bool
    {
        if ($authUser->isSuperAdmin()) {
            return true;
        }

        if (!$authUser->isClubAdmin()) {
            return false;
        }

        // Provera aktivne pretplate
        $subscription = $authUser->subscription;
        if (!$subscription || $subscription->status !== 'active') {
            return false;
        }

        // Provera limita za timove
        $currentTeamsCount = \App\Models\Team::where('club_id', $authUser->club_id)->count();

        return $currentTeamsCount < $subscription->max_teams;
    }

    public function update(User $authUser, Team $team): bool
    {
        if ($authUser->isSuperAdmin()) {
            return true;
        }

        return $authUser->isClubAdmin() && (int) $authUser->club_id === (int) $team->club_id;
    }

    public function delete(User $authUser, Team $team): bool
    {
        if ($authUser->isSuperAdmin()) {
            return true;
        }

        return $authUser->isClubAdmin() && (int) $authUser->club_id === (int) $team->club_id;
    }
}