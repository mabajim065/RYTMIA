<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeGimnastaMayorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;


    // CONSTRUCTOR
    // Recibe los datos de la gimnasta mayor de edad para personalizar el mensaje

    public function __construct(User $user)
    {
        $this->user = $user;
    }


    // ENCABEZADO DEL CORREO (ENVELOPE)
    // Define el asunto principal con el que el mensaje llegará a la bandeja de entrada

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Te damos la bienvenida a Rytmia!',
        );
    }


    // PLANTILLA DEL CORREO (CONTENT)
    // Vincula el envío con su vista Blade correspondiente

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome_gimnasta_mayor',
        );
    }


    // ARCHIVOS ADJUNTOS
    // Define si el correo incluye documentos anexos (vacío en este caso)

    public function attachments(): array
    {
        return [];
    }
}