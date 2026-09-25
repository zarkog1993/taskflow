<?php

namespace App\Http\Controllers;

use App\Models\MatchDay;
use App\Models\Player;
use App\Services\EventInvitationService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;

class EventInvitationController extends Controller
{
    public function __construct(private EventInvitationService $invitationService) {}

    /**
     * RSVP odgovor igrača iz email pozivnice (potpisan link, bez prijave).
     */
    public function respond(string $type, int $event, Player $player, string $status): RedirectResponse
    {
        $modelClass = Relation::getMorphedModel($type);
        abort_unless($modelClass, 404);

        $eventModel = $modelClass::findOrFail($event);
        abort_unless($eventModel->invitedPlayers()->whereKey($player->id)->exists(), 404);

        $this->invitationService->respond($eventModel, $player, $status);

        // Preusmeravanje na Vue stranicu potvrde
        $eventName = $eventModel instanceof MatchDay ? "Utakmica vs {$eventModel->opponent}" : $eventModel->title;
        $query = http_build_query([
            'status' => $status === 'accepted' ? 'attended' : 'absent',
            'event' => $eventName,
        ]);

        return redirect()->away(rtrim(config('app.frontend_url'), '/') . "/rsvp-confirmation?{$query}");
    }
}
