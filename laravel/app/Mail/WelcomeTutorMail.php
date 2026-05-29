<?php

namespace App\Mail;

use App\Models\User;
use App\Models\TutorLegal;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeTutorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $tutor;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, TutorLegal $tutor)
    {
        $this->user = $user;
        $this->tutor = $tutor;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registro de menor y credenciales de acceso · Rytmia',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome_tutor',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
