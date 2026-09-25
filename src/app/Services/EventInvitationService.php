<?php

namespace App\Services;

use App\Models\MatchDay;
use App\Models\Player;
use App\Models\TrainingSession;
use App\Notifications\EventInvitationNotification;
use Illuminate\Support\Facades\Notification;

class EventInvitationService
{
    /**
     * Poziva izabrane igrače (ili ceo tim ako nijedan nije izabran) na događaj
     * i šalje im email pozivnicu sa RSVP linkovima.
     */
    public function invite(TrainingSession|MatchDay $event, array $playerIds = []): void
    {
        $players = Player::withoutGlobalScopes()
            ->where('team_id', $event->team_id)
            ->when($playerIds, fn ($q) => $q->whereIn('id', $playerIds))
            ->get();

        if ($players->isEmpty()) {
            return;
        }

        $event->invitedPlayers()->syncWithoutDetaching($players->pluck('id'));

        $recipients = $players->filter(fn (Player $player) => filled($player->email));

        if ($recipients->isEmpty()) {
            return;
        }

        try {
            Notification::send($recipients, new EventInvitationNotification($event->loadMissing('team')));
        } catch (\Throwable $e) {
            // Događaj i pozivnice su već sačuvani - neuspelo slanje maila ne sme da obori zahtev
            report($e);
        }
    }

    public function respond(TrainingSession|MatchDay $event, Player $player, string $status): void
    {
        $event->invitedPlayers()->updateExistingPivot($player->id, [
            'status' => $status,
            'responded_at' => now(),
        ]);
    }
}
