<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservaResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'fecha'          => $this->fecha,
            'hora_inicio'    => $this->hora_inicio ? substr($this->hora_inicio, 0, 5) : null,
            'hora_fin'       => $this->hora_fin    ? substr($this->hora_fin,    0, 5) : null,
            'total_personas' => $this->total_personas,
            'estado'         => $this->estado_en_vivo,
            'motivo_cancelacion' => $this->motivo_cancelacion,
            'decidido_por'   => $this->whenLoaded('decididoPor', fn() => $this->decididoPor?->name),
            'cancelado_por'  => $this->whenLoaded('canceladoPor', fn() => $this->canceladoPor?->name),
            'embarcacion'    => new EmbarcacionResource($this->whenLoaded('embarcacion')),
            'pasajeros'      => PasajeroResource::collection($this->whenLoaded('pasajeros')),
            'boleto'         => $this->boleto ? new BoletoResource($this->boleto) : null,
            'viaje'          => $this->whenLoaded('viaje', fn() => $this->viaje ? [
                'id'     => $this->viaje->id,
                'estado' => $this->viaje->estado,
            ] : null),
            'user'           => $this->whenLoaded('user', fn() => [
                'id'       => $this->user->id,
                'nombre'   => $this->user->name,
                'email'    => $this->user->email,
                'cedula'   => $this->user->cedula,
                'telefono' => $this->user->telefono,
            ]),
            'creado_en'      => $this->created_at->format('d/m/Y H:i'),
        ];
    }
}