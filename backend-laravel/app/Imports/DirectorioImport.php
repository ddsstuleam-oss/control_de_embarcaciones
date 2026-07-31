<?php

namespace App\Imports;

use App\Models\DirectorioPersona;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class DirectorioImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public int $importados = 0;
    public int $actualizados = 0;

    public function model(array $row)
    {
        $persona = DirectorioPersona::where('cedula', $row['cedula'])->first();

        if ($persona) {
            $persona->update([
                'nombre'   => $row['nombre'],
                'tipo'     => $row['tipo']    ?? 'externo',
                'carrera'  => $row['carrera']  ?? null,
                'facultad' => $row['facultad'] ?? null,
                'telefono' => $row['telefono'] ?? null,
                'email'    => $row['email']    ?? null,
            ]);
            $this->actualizados++;
            return null;
        }

        $this->importados++;

        return new DirectorioPersona([
            'cedula'   => $row['cedula'],
            'nombre'   => $row['nombre'],
            'tipo'     => $row['tipo']    ?? 'externo',
            'carrera'  => $row['carrera']  ?? null,
            'facultad' => $row['facultad'] ?? null,
            'telefono' => $row['telefono'] ?? null,
            'email'    => $row['email']    ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'cedula' => 'required|digits:10',
            'nombre' => 'required|string|max:255',
            'tipo'   => 'nullable|in:estudiante,docente,administrativo,externo',
        ];
    }
}