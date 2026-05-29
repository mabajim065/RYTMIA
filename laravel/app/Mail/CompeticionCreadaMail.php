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

    // Recibe la competición y el usuario al que se le envía el correo
    public function __construct(Competicion $competicion, User $user)
    {
        $this->competicion = $competicion;
        $this->user = $user;
    }

    // Asunto del correo según el rol del usuario
    public function envelope(): Envelope
    {
        $subject = $this->user->esEntrenadora()
            ? 'Convocatoria como entrenadora para la competición: ' . $this->competicion->nombre
            : 'Has sido seleccionada para una nueva competición: ' . $this->competicion->nombre;

        return new Envelope(
            subject: $subject,
        );
    }

    // Vista del correo y datos que necesita
    public function content(): Content
    {
        return new Content(
            view: 'emails.competicion_creada',
            with: [
                'competicion' => $this->competicion,
                'user' => $this->user,
            ],
        );
    }

    // Sin archivos adjuntos
    public function attachments(): array
    {
        return [];
    }
}