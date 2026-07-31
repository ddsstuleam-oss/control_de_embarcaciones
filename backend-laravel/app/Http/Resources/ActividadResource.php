<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ActividadResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'accion'      => $this->accion,
            'descripcion' => $this->descripcion,
            'ip'          => $this->ip,
            'dispositivo' => $this->dispositivo,
            'fecha'       => $this->created_at->format('d/m/Y H:i'),
        ];
    }
}