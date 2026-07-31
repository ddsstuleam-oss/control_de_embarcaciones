<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PasajeroResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'       => $this->id,
            'nombre'   => $this->nombre,
            'cedula'   => $this->cedula,
            'tipo'     => $this->tipo,
            'carrera'  => $this->carrera,
            'facultad' => $this->facultad,
            'telefono' => $this->telefono,
            'email'    => $this->email,
            'presente' => $this->presente,
            'hora_abordaje'          => $this->hora_abordaje?->format('d/m/Y H:i'),
            'observaciones_embarque' => $this->observaciones_embarque,
            'llego'                  => $this->llego,
            'hora_llegada'           => $this->hora_llegada?->format('d/m/Y H:i'),
            'observaciones_llegada'  => $this->observaciones_llegada,
        ];
    }
}