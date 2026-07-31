<?php

namespace App\Exports;

use App\Models\Reserva;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PasajerosExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private string $fecha,
        private ?int $embarcacion_id = null
    ) {}

    public function collection()
    {
        return Reserva::with('pasajeros', 'embarcacion', 'user')
            ->whereDate('fecha', $this->fecha)
            ->whereNotIn('estado', ['cancelada'])
            ->when($this->embarcacion_id, fn($q) =>
                $q->where('embarcacion_id', $this->embarcacion_id)
            )
            ->get();
    }

    public function map($reserva): array
    {
        $rows = [];
        foreach ($reserva->pasajeros as $p) {
            $rows[] = [
                str_pad($reserva->id, 6, '0', STR_PAD_LEFT),
                $reserva->embarcacion->nombre,
                \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y'),
                $reserva->hora_inicio ? substr($reserva->hora_inicio, 0, 5) : '—',
                $reserva->hora_fin    ? substr($reserva->hora_fin, 0, 5)    : '—',
                $reserva->user->name,
                $p->nombre,
                $p->cedula,
                ucfirst($p->tipo ?? 'externo'),
                $p->carrera ?? '—',
                $p->facultad ?? '—',
                $p->telefono ?? '—',
                $p->email ?? '—',
                strtoupper($reserva->estado),
            ];
        }
        return $rows;
    }

    public function headings(): array
    {
        return [
            'N° Reserva',
            'Embarcación',
            'Fecha',
            'Hora Inicio',
            'Hora Fin',
            'Titular',
            'Nombre Pasajero',
            'Cédula',
            'Tipo',
            'Carrera',
            'Facultad',
            'Teléfono',
            'Email',
            'Estado',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1A1A2E'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Pasajeros ' . $this->fecha;
    }
}