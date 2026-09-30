<?php

namespace App\Services;

use App\Models\TrainingSession;
use App\Models\User;

class TrainingSessionService
{
    public function updateStatus(TrainingSession $session, string $status): TrainingSession
    {
        $session->update(['status' => $status]);
        return $session;
    }

    public function getPaginatedSessions(User $authUser, int $perPage = 15)
    {
        $query = TrainingSession::with(['team', 'creator', 'invitedPlayers']);

        if ($authUser->isClubAdmin()) {
            $query->where('club_id', $authUser->club_id);
        } elseif (!$authUser->isSuperAdmin()) {
            // Ako je igrač, prikazujemo samo treninge timova u kojima se on nalazi
            $query->whereHas('team.members', function ($q) use ($authUser) {
                $q->where('users.id', $authUser->id);
            });
        }

        return $query->latest('scheduled_at')->paginate($perPage);
    }

    public function store(array $data, User $authUser): TrainingSession
    {
        $data['club_id'] = $authUser->isSuperAdmin()
            ? \App\Models\Team::findOrFail($data['team_id'])->club_id
            : $authUser->club_id;
        $data['created_by'] = $authUser->id;

        return TrainingSession::create($data)->load(['team', 'creator']);
    }
}
