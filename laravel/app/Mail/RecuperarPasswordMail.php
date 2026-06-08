<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecuperarPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl;
    public $user;


    // CONSTRUCTOR
    // Recibe los datos dinámicos: la URL temporal y el usuario que solicitó el reseteo

    public function __construct(string $resetUrl, User $user)
    {
        $this->resetUrl = $resetUrl;
        $this->user = $user;
    }


    // ENCABEZADO DEL CORREO (ENVELOPE)
    // Define el asunto principal con el que llegará a la bandeja de entrada

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Restablecer contraseña · Rytmia',
        );
    }


    // PLANTILLA DEL CORREO (CONTENT)
    // Vincula el envío con su vista Blade correspondiente

    public function content(): Content
    {
        return new Content(
            view: 'emails.recuperar_password',
        );
    }


    // ARCHIVOS ADJUNTOS
    // Define si el correo incluye documentos anexos (vacío en este caso)

    public function attachments(): array
    {
        return [];
    }
}