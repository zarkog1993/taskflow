<?php

namespace App\Policies;

use App\Models\TrainingSession;
use App\Models\User;

class TrainingSessionPolicy
{
    public function viewAny(User $authUser): bool
    {
        return true;
    }

    public function view(User $authUser, TrainingSession $trainingSession): bool
    {
        if ($authUser->isSuperAdmin()) {
            return true;
        }

        return (int) $authUser->club_id === (int) $trainingSession->club_id;
    }

    public function create(User $authUser): bool
    {
        return $authUser->isSuperAdmin()
            || ($authUser->isClubAdmin() && $authUser->hasActiveSubscription());
    }

    public function update(User $authUser, TrainingSession $trainingSession): bool
    {
        if ($authUser->isSuperAdmin()) {
            return true;
        }

        return $authUser->isClubAdmin()
            && $authUser->hasActiveSubscription()
            && (int) $authUser->club_id === (int) $trainingSession->club_id;
    }

    public function delete(User $authUser, TrainingSession $trainingSession): bool
    {
        if ($authUser->isSuperAdmin()) {
            return true;
        }

        return $authUser->isClubAdmin()
            && $authUser->hasActiveSubscription()
            && (int) $authUser->club_id === (int) $trainingSession->club_id;
    }
}