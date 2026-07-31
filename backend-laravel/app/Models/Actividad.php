<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades'; // ← forzar nombre correcto
    protected $fillable = [
        'user_id',
        'accion',
        'descripcion',
        'ip',
        'dispositivo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}