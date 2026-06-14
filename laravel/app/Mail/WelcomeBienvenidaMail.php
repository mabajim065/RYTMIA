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


    // recibe el usuario
    public function __construct(User $user)
    {
        $this->user = $user;
    }


   // asunto del correo
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Bienvenida/o a Rytmia! Tus credenciales de acceso',
        );
    }

    // vista del correo
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome_bienvenida',
        );
    }


    public function attachments(): array
    {
        return [];
    }
}