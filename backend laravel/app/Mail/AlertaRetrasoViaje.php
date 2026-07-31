<?php

namespace App\Mail;

use App\Models\Viaje;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class AlertaRetrasoViaje extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Viaje $viaje) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Alerta: retraso en viaje #' . $this->viaje->id . ' — ' . $this->viaje->embarcacion->nombre,
        );
    }

    public function content(): Content
    {
        $this->withSymfonyMessage(function (Email $email) {
            $email->embedFromPath(public_path('images/uleam-logo.png'),   'logo',   'image/png');
            $email->embedFromPath(public_path('images/uleam-banner.jpg'), 'banner', 'image/jpeg');
        });

        return new Content(
            view: 'emails.alerta-retraso-viaje',
            with: [
                'appName' => config('app.name', 'ULEAM APP'),
            ],
        );
    }
}
