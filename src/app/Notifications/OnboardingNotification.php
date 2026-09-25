<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OnboardingNotification extends Notification
{
    use Queueable;

    public function __construct(public string $onboardingUrl) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Aktivacija Vašeg Kluba - Pravi Fudbal')
            ->greeting('Zdravo ' . $notifiable->name . '!')
            ->line('Uspešno ste kreirali nalog za vaš klub. Kliknite na dugme ispod kako biste izabrali paket pretplate i završili onboarding:')
            ->action('Završi Onboarding', $this->onboardingUrl)
            ->line('Link važi 24 časa.')
            ->line('Hvala vam što koristite Pravi Fudbal!');
    }
}
