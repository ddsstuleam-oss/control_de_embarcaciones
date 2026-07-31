<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Boleto;

class Reserva extends Model
{
    use SoftDeletes;
    protected $table = 'reservas';

    protected $fillable = [
        'user_id',
        'embarcacion_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'total_personas',
        'estado',
        'motivo_cancelacion',
        'decidido_por_id',
        'cancelado_por_id',
    ];

    protected $casts = [
        'fecha'       => 'date:Y-m-d',
        'hora_inicio' => 'string',
        'hora_fin'    => 'string',
    ];

    public function boleto()
{
    return $this->hasOne(Boleto::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}

public function pasajeros()
{
    return $this->hasMany(Pasajero::class);
}

public function embarcacion()
{
    return $this->belongsTo(Embarcacion::class);
}

public function boletos()
{
    return $this->hasMany(Boleto::class);
}

public function viaje()
{
    return $this->hasOne(Viaje::class);
}

public function decididoPor()
{
    return $this->belongsTo(User::class, 'decidido_por_id');
}

public function canceladoPor()
{
    return $this->belongsTo(User::class, 'cancelado_por_id');
}

/**
 * Estado calculado en tiempo real: si la reserva sigue "confirmada" pero
 * su boleto ya venció sin usarse, o sigue "pendiente" pero la hora del
 * viaje ya pasó sin ser aprobada, refleja "vencida" aunque la BD todavía
 * no haya sido actualizada por el self-heal o el job programado.
 */
public function getEstadoEnVivoAttribute(): string
{
    // El boleto puede ya estar persistido como "vencido" (job programado /
    // self-heal previo) o recién detectarse vencido en este momento — ambos
    // casos deben reflejar la reserva como vencida.
    if ($this->estado === 'confirmada' && $this->boleto &&
        ($this->boleto->estado === 'vencido' || $this->boleto->estaVencido())) {
        return 'vencida';
    }

    if ($this->estaVencidaPendiente()) {
        return 'vencida';
    }

    return $this->estado;
}

/**
 * Una reserva "pendiente" está vencida cuando la hora de inicio del viaje
 * ya pasó sin que un admin la haya aprobado ni rechazado.
 */
public function estaVencidaPendiente(): bool
{
    if ($this->estado !== 'pendiente') {
        return false;
    }

    $horaInicio = \Carbon\Carbon::parse(
        $this->fecha->toDateString() . ' ' . $this->hora_inicio
    );

    return now()->gt($horaInicio);
}

/**
 * Persiste la reserva pendiente como vencida (libera el horario y refleja
 * el estado real en cualquier pantalla o filtro que use la columna cruda).
 */
public function marcarVencida(): void
{
    $this->update(['estado' => 'vencida']);
}

}