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

class ReservasExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private string $desde,
        private string $hasta,
        private ?int $embarcacion_id = null
    ) {}

    public function collection()
    {
        return Reserva::with('embarcacion', 'user', 'pasajeros')
            ->whereBetween('fecha', [$this->desde, $this->hasta])
            ->when($this->embarcacion_id, fn($q) =>
                $q->where('embarcacion_id', $this->embarcacion_id)
            )
            ->orderBy('fecha')
            ->get();
    }

    public function map($reserva): array
    {
        return [
            str_pad($reserva->id, 6, '0', STR_PAD_LEFT),
            \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y'),
            $reserva->embarcacion->nombre,
            $reserva->hora_inicio ? substr($reserva->hora_inicio, 0, 5) : '—',
            $reserva->hora_fin    ? substr($reserva->hora_fin, 0, 5)    : '—',
            $reserva->user->name,
            $reserva->user->cedula,
            $reserva->total_personas,
            $reserva->pasajeros->pluck('nombre')->implode(', '),
            strtoupper($reserva->estado),
            $reserva->created_at->format('d/m/Y H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'N° Reserva',
            'Fecha',
            'Embarcación',
            'Hora Inicio',
            'Hora Fin',
            'Titular',
            'Cédula Titular',
            'Total Pasajeros',
            'Pasajeros',
            'Estado',
            'Fecha Registro',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE63946'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Reservas ' . $this->desde . ' al ' . $this->hasta;
    }
}