<?php

namespace App\Mail;

use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\Mime\Email;

class ReservaConfirmada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Reserva $reserva) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Reserva #' . str_pad($this->reserva->id, 6, '0', STR_PAD_LEFT),
        );
    }

    public function content(): Content
    {
        $this->withSymfonyMessage(function (Email $email) {
            $email->embedFromPath(public_path('images/uleam-logo.png'),   'logo',   'image/png');
            $email->embedFromPath(public_path('images/uleam-banner.jpg'), 'banner', 'image/jpeg');
        });

        return new Content(
            view: 'emails.reserva-confirmada',
            with: [
                'appName' => config('app.name', 'ULEAM APP'),
            ],
        );
    }

    public function attachments(): array
    {
        // Generar QR en base64 (para incrustar en PDF)
        $qrBase64 = base64_encode(
            QrCode::format('svg')
                ->size(200)
                ->errorCorrection('H')
                ->generate($this->reserva->boleto->codigo_qr)
        );

        // Logo
        $logoPath   = public_path('images/logo-uleam-nuevo.png');
        $logoBase64 = file_exists($logoPath)
            ? base64_encode(file_get_contents($logoPath))
            : null;

        // Generar PDF en memoria
        $pdf = Pdf::loadView('pdf.ticket', [
            'boleto'      => $this->reserva->boleto,
            'reserva'     => $this->reserva,
            'embarcacion' => $this->reserva->embarcacion,
            'pasajeros'   => $this->reserva->pasajeros,
            'usuario'     => $this->reserva->user,
            'logoBase64'  => $logoBase64,
            'qrBase64'    => $qrBase64,
        ])
        ->setPaper([0, 0, 396, 612], 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'sans-serif',
        ]);

        $nombreArchivo = 'boleto-' . $this->reserva->boleto->codigo_qr . '.pdf';

        return [
            Attachment::fromData(
                fn() => $pdf->output(),
                $nombreArchivo
            )->withMime('application/pdf'),
        ];
    }
}