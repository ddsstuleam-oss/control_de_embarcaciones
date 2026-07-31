<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
    protected $fillable = [
        'reserva_id',
        'embarcacion_id',
        'operador_id',
        'estado',
        'hora_programada_salida',
        'hora_programada_llegada',
        'hora_real_salida',
        'hora_real_llegada',
        'usuario_cierre_id',
        'observaciones_cierre',
        'fecha_finalizacion',
    ];

    protected $casts = [
        'hora_programada_salida'  => 'datetime',
        'hora_programada_llegada' => 'datetime',
        'hora_real_salida'        => 'datetime',
        'hora_real_llegada'       => 'datetime',
        'fecha_finalizacion'      => 'datetime',
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

    public function embarcacion()
    {
        return $this->belongsTo(Embarcacion::class);
    }

    public function operador()
    {
        return $this->belongsTo(User::class, 'operador_id');
    }

    public function usuarioCierre()
    {
        return $this->belongsTo(User::class, 'usuario_cierre_id');
    }

    public function evidencias()
    {
        return $this->hasMany(ViajeEvidencia::class);
    }

    public function alertas()
    {
        return $this->hasMany(ViajeAlerta::class);
    }
}
