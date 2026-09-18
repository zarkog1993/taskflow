<?php

namespace App\Notifications;

use App\Models\TrainingSession;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

class TrainingScheduledNotification extends Notification
{
    use Queueable;

    public function __construct(public TrainingSession $session) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // 1. Generisanje potpisanih (Signed) URL-ova za RSVP koji važe 3 dana
        $confirmUrl = URL::temporarySignedRoute(
            'trainings.rsvp',
            now()->addDays(3),
            ['session' => $this->session->id, 'user' => $notifiable->id, 'status' => 'attended']
        );

        $declineUrl = URL::temporarySignedRoute(
            'trainings.rsvp',
            now()->addDays(3),
            ['session' => $this->session->id, 'user' => $notifiable->id, 'status' => 'absent']
        );

        // 2. Osiguravamo da je scheduled_at validan Carbon objekat
        $scheduledAt = $this->session->scheduled_at instanceof \DateTimeInterface
            ? $this->session->scheduled_at
            : Carbon::parse($this->session->scheduled_at);

        // 3. Kreiranje .ics kalendarskog priloga
        $icsContent = Calendar::create($this->session->team->name ?? 'TaskFlow')
            ->event(
                Event::create($this->session->title)
                    ->startsAt($scheduledAt)
                    ->endsAt($scheduledAt->copy()->addMinutes(90))
                    ->address($this->session->location ?? 'Glavni teren')
                    ->description("Trening zakazan preko TaskFlow aplikacije.")
            )->get();

        // 4. Sastavljanje MailMessage objekta
        return (new MailMessage)
            ->subject("⚽ Novi Trening: {$this->session->title}")
            ->greeting("Zdravo {$notifiable->name},")
            ->line("Zakazan je novi trening za tvoju ekipu.")
            ->line("📅 **Datum i vreme:** " . $scheduledAt->format('d.m.Y. u H:i'))
            ->line("📍 **Lokacija:** " . ($this->session->location ?? 'Glavni teren'))
            ->line("Potvrdi svoje prisustvo jednim klikom:")
            ->action('✅ Dolazim na Trening', $confirmUrl)
            ->line("Ukoliko ne možeš doći, klikni ovde: [❌ Ne dolazim]({$declineUrl})")
            ->attachData($icsContent, 'trening.ics', [
                'mime' => 'text/calendar; charset=UTF-8; method=REQUEST',
            ]);
    }
}
