<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PlantillaDirectorioExport implements FromArray, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return ['cedula', 'nombre', 'tipo', 'carrera', 'facultad', 'telefono', 'email'];
    }

    public function array(): array
    {
        return [
            ['1300000001', 'Juan Pérez García', 'estudiante', 'Biología', 'Facultad Ciencias de la Vida y Tecnologías', '0991234567', 'juan@uleam.edu.ec'],
            ['1300000002', 'María López Vera',  'docente',    'Administración de Empresas', 'Facultad Ciencias Administrativas, Contables y Comercio', '0997654321', 'maria@uleam.edu.ec'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1A1A2E']],
            ],
        ];
    }
}