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


    // recibe el usuario y el tutor legal
    public function __construct(User $user, TutorLegal $tutor)
    {
        $this->user = $user;
        $this->tutor = $tutor;
    }


    // asunto del correo

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registro de menor y credenciales de acceso · Rytmia',
        );
    }


    // vista del correo
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome_tutor',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}