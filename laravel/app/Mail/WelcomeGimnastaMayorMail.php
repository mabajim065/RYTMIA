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


    // recibe el usuario
    public function __construct(User $user)
    {
        $this->user = $user;
    }


   // asunto del correo
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Te damos la bienvenida a Rytmia!',
        );
    }


       // vista del correo
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome_gimnasta_mayor',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}