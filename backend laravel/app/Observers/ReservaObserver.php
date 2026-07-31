<?php

namespace App\Observers;

use App\Models\Reserva;
use App\Models\Actividad;

class ReservaObserver
{
    public function created(Reserva $reserva): void
    {
        Actividad::create([
            'user_id'     => $reserva->user_id,
            'accion'      => 'reserva_creada',
            'descripcion' => 'Reserva #' . str_pad($reserva->id, 6, '0', STR_PAD_LEFT) .
                             ' — ' . $reserva->embarcacion->nombre .
                             ' para el ' . \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') .
                             ' — ' . $reserva->total_personas . ' pasajero(s)',
            'ip'          => request()->ip(),
            'dispositivo' => request()->header('User-Agent'),
        ]);
    }

    public function updated(Reserva $reserva): void
    {
        // Solo registrar cuando cambia el estado
        if (!$reserva->wasChanged('estado')) {
            return;
        }

        $accion = match($reserva->estado) {
            'confirmada'  => 'reserva_aprobada',
            'cancelada'   => 'reserva_cancelada',
            'rechazada'   => 'reserva_rechazada',
            'completada'  => 'reserva_completada',
            'vencida'     => 'reserva_vencida',
            default       => 'reserva_actualizada',
        };

        $descripcion = 'Reserva #' . str_pad($reserva->id, 6, '0', STR_PAD_LEFT) .
                       ' — Estado cambiado a: ' . strtoupper($reserva->estado);

        if (in_array($reserva->estado, ['cancelada', 'rechazada']) && $reserva->motivo_cancelacion) {
            $descripcion .= ' — Motivo: ' . $reserva->motivo_cancelacion;
        }

        // Si quien ejecutó la acción no es el dueño de la reserva (p. ej. un
        // admin aprobando/rechazando/cancelando), dejar constancia de quién
        // fue — el "user_id" del registro sigue siendo el dueño para que su
        // feed personal de actividad no se vea afectado.
        if (auth()->check() && auth()->id() !== $reserva->user_id) {
            $descripcion .= ' — por ' . auth()->user()->name;
        }

        Actividad::create([
            'user_id'     => $reserva->user_id,
            'accion'      => $accion,
            'descripcion' => $descripcion,
            'ip'          => request()->ip(),
            'dispositivo' => request()->header('User-Agent'),
        ]);
    }

    public function deleted(Reserva $reserva): void
    {
        Actividad::create([
            'user_id'     => auth()->id() ?? $reserva->user_id,
            'accion'      => 'reserva_eliminada',
            'descripcion' => 'Reserva #' . str_pad($reserva->id, 6, '0', STR_PAD_LEFT) .
                             ' eliminada por administrador',
            'ip'          => request()->ip(),
            'dispositivo' => request()->header('User-Agent'),
        ]);
    }
}