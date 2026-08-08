<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

protected $fillable = [
    'cedula',
    'name',
    'email',
    'email_pendiente',
    'telefono',
    'password',
    'password_changed_at',
    'activo',
    'email_verified_at',
    'email_verification_code',
    'email_code_expires_at',
    'foto_perfil',
];

protected $hidden = [
    'password',
    'remember_token',
];

protected $casts = [
    'email_verified_at'     => 'datetime',
    'password_changed_at'   => 'datetime',
    'email_code_expires_at' => 'datetime',
    'password'              => 'hashed',
];

public function reservas()
{
    return $this->hasMany(Reserva::class);
}  

public function actividades()
{
    return $this->hasMany(Actividad::class);
}

}
