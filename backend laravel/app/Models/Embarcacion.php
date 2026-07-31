<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Embarcacion extends Model
{
    protected $table = 'embarcaciones'; // ← agregar esto

    protected $fillable = [
        'nombre',
        'capacidad',
        'estado',
        'descripcion',
        'imagen',
    ];

    protected $appends = ['imagen_url'];

    public function getImagenUrlAttribute(): ?string
    {
        return $this->imagen
            ? asset('media/' . $this->imagen)
            : null;
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    public function cuposDisponibles(string $fecha): int
    {
        $reservados = $this->reservas()
            ->where('fecha', $fecha)
            ->whereNotIn('estado', ['cancelada', 'rechazada'])
            ->sum('total_personas');

        return max(0, $this->capacidad - $reservados);
    }
}