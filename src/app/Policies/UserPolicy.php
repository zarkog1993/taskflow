<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Pregled liste korisnika: Admini i klupski admini vide svoje opsege.
     */
    public function viewAny(User $authUser): bool
    {
        return true; // Svi autentifikovani korisnici mogu dohvatiti listu (filtrirano kroz UserService)
    }

    /**
     * Pregled pojedinačnog korisnika.
     */
    public function view(User $authUser, User $targetUser): bool
    {
        if ($authUser->isSuperAdmin() || $authUser->id === $targetUser->id) {
            return true;
        }

        return $authUser->isClubAdmin() && (int)$authUser->club_id === (int)$targetUser->club_id;
    }

    /**
     * Kreiranje novog korisnika.
     */
    public function create(User $authUser): bool
    {
        return $authUser->isSuperAdmin() || $authUser->isClubAdmin() || true;
    }

    /**
     * Ažuriranje korisnika.
     */
    public function update(User $authUser, User $targetUser): bool
    {
        if ($authUser->isSuperAdmin() || $authUser->id === $targetUser->id) {
            return true;
        }

        if ($authUser->isClubAdmin()) {
            return (int)$authUser->club_id === (int)$targetUser->club_id && !$targetUser->isSuperAdmin();
        }

        return false;
    }

    /**
     * Brisanje korisnika.
     */
    public function delete(User $authUser, User $targetUser): bool
    {
        // Korisnik uvek može da obriše samog sebe
        if ($authUser->id === $targetUser->id) {
            return true;
        }

        // Super Admin može obrisati bilo koga
        if ($authUser->isSuperAdmin()) {
            return true;
        }

        // Club Admin može obrisati igrača u svom klubu
        if ($authUser->isClubAdmin()) {
            return (int)$authUser->club_id === (int)$targetUser->club_id
                && !$targetUser->isClubAdmin()
                && !$targetUser->isSuperAdmin();
        }

        return false;
    }
}
