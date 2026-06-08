<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeBienvenidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;


    // CONSTRUCTOR
    // Recibe los datos del usuario recién registrado para poder personalizar el mensaje

    public function __construct(User $user)
    {
        $this->user = $user;
    }


    // ENCABEZADO DEL CORREO (ENVELOPE)
    // Define el asunto principal con el que el mensaje llegará a la bandeja de entrada

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Bienvenida/o a Rytmia! Tus credenciales de acceso',
        );
    }


    // PLANTILLA DEL CORREO (CONTENT)
    // Vincula el envío con su vista Blade correspondiente

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome_bienvenida',
        );
    }


    // ARCHIVOS ADJUNTOS
    // Define si el correo incluye documentos anexos (vacío en este caso)

    public function attachments(): array
    {
        return [];
    }
}