<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DirectorioPersona extends Model
{
    protected $table = 'directorio_personas';

    protected $fillable = [
        'cedula',
        'nombre',
        'tipo',
        'carrera',
        'facultad',
        'telefono',
        'email',
    ];
}