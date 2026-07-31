<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                  => $this->id,
            'cedula'              => $this->cedula,
            'nombre'              => $this->name,
            'email'               => $this->email,
            'telefono'            => $this->telefono,
            'activo'              => $this->activo,
            'rol'                 => $this->getRoleNames()->first(),
            'foto_url'            => $this->foto_perfil ? asset('media/'.$this->foto_perfil) : null,
            'dias_para_vencer'    => $this->password_changed_at
                ? max(0, 90 - \Carbon\Carbon::parse($this->password_changed_at)->diffInDays(now()))
                : null,
            'email_verified'       => !is_null($this->email_verified_at),
            'miembro_desde'        => $this->created_at->format('d/m/Y'),
        ];
    }
}