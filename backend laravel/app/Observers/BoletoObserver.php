<?php

namespace App\Observers;

use App\Models\Boleto;
use App\Models\Actividad;

class BoletoObserver
{
    public function updated(Boleto $boleto): void
    {
        if (!$boleto->wasChanged('estado')) {
            return;
        }

        $accion = match($boleto->estado) {
            'usado'     => 'boleto_validado',
            'cancelado' => 'boleto_cancelado',
            default     => 'boleto_actualizado',
        };

        $descripcion = match($boleto->estado) {
            'usado'     => 'Boleto ' . $boleto->codigo_qr . ' validado en puerto — embarque registrado',
            'cancelado' => 'Boleto ' . $boleto->codigo_qr . ' cancelado',
            default     => 'Boleto ' . $boleto->codigo_qr . ' actualizado',
        };

        // Obtener user_id desde la reserva
        $userId = $boleto->reserva?->user_id ?? auth()->id();

        Actividad::create([
            'user_id'     => $userId,
            'accion'      => $accion,
            'descripcion' => $descripcion,
            'ip'          => request()->ip(),
            'dispositivo' => request()->header('User-Agent'),
        ]);
    }
}