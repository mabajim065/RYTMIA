<?php

namespace App\Mail;

use App\Models\Competicion;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompeticionCreadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $competicion;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Competicion $competicion, User $user)
    {
        $this->competicion = $competicion;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->user->esEntrenadora()
            ? '📋 Convocatoria como entrenadora para la competición: ' . $this->competicion->nombre
            : '🏆 Has sido seleccionada para una nueva competición: ' . $this->competicion->nombre;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.competicion_creada',
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
