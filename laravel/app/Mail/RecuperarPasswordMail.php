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


       // recibe el enlace y el usuario
    public function __construct(string $resetUrl, User $user)
    {
        $this->resetUrl = $resetUrl;
        $this->user = $user;
    }


       // asunto del correo
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Restablecer contraseña · Rytmia',
        );
    }


       // vista del correo
    public function content(): Content
    {
        return new Content(
            view: 'emails.recuperar_password',
        );
    }

//defino si hay archivos adjuntos q no hay 
    public function attachments(): array
    {
        return [];
    }
}