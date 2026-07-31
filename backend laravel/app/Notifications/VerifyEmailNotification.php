<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Symfony\Component\Mime\Email;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Verifica tu correo — ' . config('app.name', 'ULEAM APP'))
            ->view('emails.verify-email', [
                'code'    => $this->code,
                'name'    => $notifiable->name ?? 'Estudiante',
                'appName' => config('app.name', 'ULEAM APP'),
                'expires' => 30,
                'support' => 'soporte@uleam.edu.ec',
            ]);

        return $mail->withSymfonyMessage(function (Email $email) {
            $email->embedFromPath(public_path('images/uleam-logo.png'),   'logo',   'image/png');
            $email->embedFromPath(public_path('images/uleam-banner.jpg'), 'banner', 'image/jpeg');
        });
    }
}
