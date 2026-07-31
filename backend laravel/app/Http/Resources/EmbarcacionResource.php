<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmbarcacionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'nombre'      => $this->nombre,
            'capacidad'   => $this->capacidad,
            'estado'      => $this->estado,
            'descripcion' => $this->descripcion,
            'imagen_url'  => $this->imagen_url,
            'creado_en'   => $this->created_at->format('d/m/Y'),
        ];
    }
}