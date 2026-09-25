<?php

namespace App\Notifications;

use App\Models\MatchDay;
use App\Models\Player;
use App\Models\TrainingSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

/**
 * Pozivnica igraču za trening ili utakmicu - sa .ics prilogom i RSVP linkovima.
 */
class EventInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(public TrainingSession|MatchDay $event) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail(Player $notifiable): MailMessage
    {
        $isMatch = $this->event instanceof MatchDay;
        $title = $isMatch ? "Utakmica: {$this->event->team?->name} vs {$this->event->opponent}" : $this->event->title;
        $location = $this->event->location ?? 'Glavni teren';
        $scheduledAt = Carbon::parse($this->event->scheduled_at);

        // 1. .ics kalendarski prilog
        $icsContent = Calendar::create($this->event->team->name ?? 'TaskFlow')
            ->event(
                Event::create($title)
                    ->startsAt($scheduledAt)
                    ->endsAt($scheduledAt->copy()->addMinutes(90))
                    ->address($location)
                    ->description($isMatch ? 'Utakmica zakazana preko TaskFlow aplikacije.' : 'Trening zakazan preko TaskFlow aplikacije.')
            )->get();

        // 2. Potpisani RSVP linkovi - važe do početka događaja (najmanje 1 dan)
        $expiresAt = $scheduledAt->isFuture() ? $scheduledAt : now()->addDay();
        $rsvpUrl = fn (string $status) => URL::temporarySignedRoute('invitations.rsvp', $expiresAt, [
            'type' => $this->event->getMorphClass(),
            'event' => $this->event->id,
            'player' => $notifiable->id,
            'status' => $status,
        ]);

        return (new MailMessage)
            ->subject($isMatch ? "🏟️ Poziv na utakmicu: vs {$this->event->opponent}" : "⚽ Novi Trening: {$title}")
            ->greeting("Zdravo {$notifiable->name},")
            ->line($isMatch ? 'Pozvan si na utakmicu.' : 'Zakazan je novi trening za tvoju ekipu.')
            ->line('📅 **Datum i vreme:** ' . $scheduledAt->format('d.m.Y. u H:i'))
            ->line('📍 **Lokacija:** ' . $location)
            ->line('Potvrdi svoje prisustvo jednim klikom:')
            ->action('✅ Dolazim', $rsvpUrl('accepted'))
            ->line("Ukoliko ne možeš doći, klikni ovde: [❌ Ne dolazim]({$rsvpUrl('declined')})")
            ->attachData($icsContent, $isMatch ? 'utakmica.ics' : 'trening.ics', [
                'mime' => 'text/calendar; charset=UTF-8; method=REQUEST',
            ]);
    }
}
