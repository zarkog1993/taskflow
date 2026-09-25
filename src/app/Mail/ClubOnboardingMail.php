<?php

namespace App\Mail;

use App\Models\Club;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClubOnboardingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Club $club,
        public string $onboardingUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Complete your club onboarding');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.club-onboarding',
            with: ['user' => $this->user, 'club' => $this->club, 'onboardingUrl' => $this->onboardingUrl],
        );
    }
}
