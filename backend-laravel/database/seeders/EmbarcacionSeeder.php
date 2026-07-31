<?php

namespace Database\Seeders;

use App\Models\Embarcacion;
use Illuminate\Database\Seeder;

class EmbarcacionSeeder extends Seeder
{
    public function run(): void
    {
        $embarcaciones = [
            [
                'nombre'      => 'Lancha Uleam I',
                'capacidad'   => 25,
                'estado'      => 'disponible',
                'descripcion' => 'Embarcación principal para recorridos académicos',
            ],
            [
                'nombre'      => 'Bote Investigación',
                'capacidad'   => 12,
                'estado'      => 'disponible',
                'descripcion' => 'Embarcación para investigación científica marina',
            ],
            [
                'nombre'      => 'Lancha Uleam II',
                'capacidad'   => 30,
                'estado'      => 'disponible',
                'descripcion' => 'Embarcación de transporte estudiantil',
            ],
            [
                'nombre'      => 'Bote Rescate',
                'capacidad'   => 8,
                'estado'      => 'mantenimiento',
                'descripcion' => 'Embarcación de apoyo y rescate',
            ],
        ];

        foreach ($embarcaciones as $e) {
            Embarcacion::create($e);
        }
    }
}