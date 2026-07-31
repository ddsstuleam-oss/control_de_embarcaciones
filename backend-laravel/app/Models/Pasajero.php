<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasajero extends Model
{
    protected $fillable = [
        'reserva_id',
        'nombre',
        'cedula',
        'tipo',
        'carrera',
        'facultad',
        'telefono',
        'email',
        'presente',
        'hora_abordaje',
        'observaciones_embarque',
        'llego',
        'hora_llegada',
        'observaciones_llegada',
    ];

    protected $casts = [
        'presente'      => 'boolean',
        'hora_abordaje' => 'datetime',
        'llego'         => 'boolean',
        'hora_llegada'  => 'datetime',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }
}