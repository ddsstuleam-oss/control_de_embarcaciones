<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class ReservaPendiente extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reserva $reserva) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Solicitud de reserva #' . str_pad($this->reserva->id, 6, '0', STR_PAD_LEFT) . ' recibida',
        );
    }

    public function content(): Content
    {
        $this->withSymfonyMessage(function (Email $email) {
            $email->embedFromPath(public_path('images/uleam-logo.png'),   'logo',   'image/png');
            $email->embedFromPath(public_path('images/uleam-banner.jpg'), 'banner', 'image/jpeg');
        });

        return new Content(
            view: 'emails.reserva-pendiente',
            with: [
                'appName' => config('app.name', 'ULEAM APP'),
            ],
        );
    }
}
