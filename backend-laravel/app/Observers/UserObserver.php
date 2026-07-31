<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Actividad;

class UserObserver
{
    public function created(User $user): void
    {
        Actividad::create([
            'user_id'     => $user->id,
            'accion'      => 'cuenta_creada',
            'descripcion' => 'Cuenta creada para ' . $user->name . ' — cédula: ' . $user->cedula,
            'ip'          => request()->ip(),
            'dispositivo' => request()->header('User-Agent'),
        ]);
    }

    public function updated(User $user): void
    {
        if (!$user->wasChanged('activo')) {
            return;
        }

        $accion      = $user->activo ? 'cuenta_activada' : 'cuenta_desactivada';
        $descripcion = $user->activo
            ? 'Cuenta de ' . $user->name . ' activada por administrador'
            : 'Cuenta de ' . $user->name . ' desactivada por administrador';

        Actividad::create([
            'user_id'     => auth()->id() ?? $user->id,
            'accion'      => $accion,
            'descripcion' => $descripcion,
            'ip'          => request()->ip(),
            'dispositivo' => request()->header('User-Agent'),
        ]);
    }

    public function deleted(User $user): void
    {
        Actividad::create([
            'user_id'     => auth()->id() ?? $user->id,
            'accion'      => 'cuenta_eliminada',
            'descripcion' => 'Cuenta de ' . $user->name . ' (' . $user->cedula . ') eliminada',
            'ip'          => request()->ip(),
            'dispositivo' => request()->header('User-Agent'),
        ]);
    }
}