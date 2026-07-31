<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class ReservaRechazada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reserva $reserva) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reserva #' . str_pad($this->reserva->id, 6, '0', STR_PAD_LEFT) . ' rechazada',
        );
    }

    public function content(): Content
    {
        $this->withSymfonyMessage(function (Email $email) {
            $email->embedFromPath(public_path('images/uleam-logo.png'),   'logo',   'image/png');
            $email->embedFromPath(public_path('images/uleam-banner.jpg'), 'banner', 'image/jpeg');
        });

        return new Content(
            view: 'emails.reserva-rechazada',
            with: [
                'appName' => config('app.name', 'ULEAM APP'),
            ],
        );
    }
}
