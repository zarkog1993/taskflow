<?php

namespace App\Services;

use App\Models\TrainingSession;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Collection;

class TrainingSessionService
{
    public function getAllSessions(User $user): Collection
    {
        // Glavni i pomoćni treneri vide sve sesije
        return TrainingSession::with(['creator:id,name', 'attendances.user:id,name'])
            ->latest('scheduled_at')
            ->get();
    }

    public function createSession(User $creator, array $data): TrainingSession
    {
        $session = TrainingSession::create([
            'created_by' => $creator->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'] ?? 'training',
            'status' => $data['status'] ?? 'planned',
            'scheduled_at' => $data['scheduled_at'],
            'location' => $data['location'] ?? null,
        ]);

        // Automatski kreiraj 'pending' prisustvo za sve igrače u sistemu
        $players = User::whereHas('roles', function ($q) {
            $q->where('slug', 'player');
        })->get();

        foreach ($players as $player) {
            Attendance::create([
                'training_session_id' => $session->id,
                'user_id' => $player->id,
                'status' => 'pending',
            ]);
        }

        return $session->load(['creator:id,name', 'attendances.user:id,name']);
    }

    public function updateStatus(TrainingSession $session, string $status): TrainingSession
    {
        $session->update(['status' => $status]);
        return $session;
    }

    public function getPaginatedSessions(User $authUser, int $perPage = 15)
    {
        $query = TrainingSession::with(['team', 'creator']);

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
