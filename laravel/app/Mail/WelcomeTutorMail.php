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


    // CONSTRUCTOR
    // Recibe los datos de la gimnasta menor y su tutor legal para personalizar el mensaje

    public function __construct(User $user, TutorLegal $tutor)
    {
        $this->user = $user;
        $this->tutor = $tutor;
    }


    // ENCABEZADO DEL CORREO (ENVELOPE)
    // Define el asunto principal con el que el mensaje llegará a la bandeja de entrada

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registro de menor y credenciales de acceso · Rytmia',
        );
    }


    // PLANTILLA DEL CORREO (CONTENT)
    // Vincula el envío con su vista Blade correspondiente

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome_tutor',
        );
    }


    // ARCHIVOS ADJUNTOS
    // Define si el correo incluye documentos anexos (vacío en este caso)

    public function attachments(): array
    {
        return [];
    }
}