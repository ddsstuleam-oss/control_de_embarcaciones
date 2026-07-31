<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BoletoResource extends JsonResource
{
    public function toArray($request): array
{
    return [
        'id'        => $this->id,
        'codigo_qr' => $this->codigo_qr,
        'estado'    => $this->estado_en_vivo,
        'pdf_url'   => $this->pdf_url,
        'usado_en'  => $this->usado_en?->format('d/m/Y H:i'),
        'reserva'   => $this->whenLoaded('reserva', fn() => [
            'id'             => $this->reserva->id,
            'fecha'          => $this->reserva->fecha,
            'hora_inicio'    => $this->reserva->hora_inicio,
            'hora_fin'       => $this->reserva->hora_fin,
            'total_personas' => $this->reserva->total_personas,
            'estado'         => $this->reserva->estado,
            'embarcacion'    => $this->reserva->embarcacion ? [
                'id'       => $this->reserva->embarcacion->id,
                'nombre'   => $this->reserva->embarcacion->nombre,
                'capacidad'=> $this->reserva->embarcacion->capacidad,
            ] : null,
            'pasajeros' => $this->reserva->pasajeros->map(fn($p) => [
                'id'     => $p->id,
                'nombre' => $p->nombre,
                'cedula' => $p->cedula,
                'tipo'   => $p->tipo,
            ]),
        ]),
    ];
}
}