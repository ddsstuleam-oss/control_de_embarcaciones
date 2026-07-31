<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Symfony\Component\Mime\Email;

class PasswordResetNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Expiración desde config (fallback 60)
        $pwdBroker = config('auth.defaults.passwords', 'users');
        $expires   = config("auth.passwords.$pwdBroker.expire", 60);

        $mail = (new MailMessage)
            ->subject('Recuperación de contraseña — ' . config('app.name', 'ULEAM APP'))
            ->view('emails.uleam_password_reset', [
                'token'     => $this->token,
                'name'      => $notifiable->name ?? 'Estudiante',
                'appName'   => config('app.name', 'ULEAM APP'),
                'actionUrl' => null, // si luego tienes front, lo activamos
                'expires'   => $expires,
                'support'   => 'soporte@uleam.edu.ec',
            ]);

        // Embebe imágenes (CID) desde public/images
        return $mail->withSymfonyMessage(function (Email $email) {
            $email->embedFromPath(public_path('images/uleam-logo.png'),   'logo',   'image/png');
            $email->embedFromPath(public_path('images/uleam-banner.jpg'), 'banner', 'image/jpeg');
        });
    }
}
