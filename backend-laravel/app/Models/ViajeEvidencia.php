<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViajeEvidencia extends Model
{
    protected $fillable = [
        'viaje_id',
        'ruta',
        'tipo',
        'user_id',
    ];

    public function viaje()
    {
        return $this->belongsTo(Viaje::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
