<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViajeAlerta extends Model
{
    protected $fillable = [
        'viaje_id',
        'tipo',
        'mensaje',
    ];

    public function viaje()
    {
        return $this->belongsTo(Viaje::class);
    }
}
