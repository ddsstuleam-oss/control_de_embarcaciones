<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\PasajerosExport;
use App\Exports\ReservasExport;
use App\Models\Embarcacion;
use App\Models\Reserva;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @group Admin — Reportes
 *
 * Endpoints para generación y descarga de reportes en Excel y PDF.
 * Todos los endpoints requieren rol admin.
 */
class ReporteController extends Controller
{
    /**
     * Excel — pasajeros por fecha
     *
     * Genera y descarga un archivo Excel con el listado completo de pasajeros
     * de todas las reservas activas para una fecha específica.
     * Incluye: N° reserva, embarcación, titular, nombre pasajero, cédula, tipo, carrera, teléfono, email, estado.
     *
     * @authenticated
     *
     * @queryParam fecha string required Fecha de embarque (YYYY-MM-DD). Example: 2026-05-10
     * @queryParam embarcacion_id integer Filtrar por embarcación específica. Example: 1
     *
     * @response 200 scenario="Éxito" <<binary>> Archivo Excel (.xlsx) con listado de pasajeros.
     * @response 422 scenario="Validación" {
     *   "message": "Error de validación",
     *   "errors": {"fecha": ["The fecha field is required."]}
     * }
     */
    public function excelPasajeros(Request $request)
    {
        $request->validate([
            'fecha'          => 'required|date',
            'embarcacion_id' => 'nullable|exists:embarcaciones,id',
        ]);

        $filename = 'pasajeros-' . $request->fecha . '.xlsx';

        return Excel::download(
            new PasajerosExport($request->fecha, $request->embarcacion_id),
            $filename
        );
    }

    /**
     * Excel — reservas por rango de fechas
     *
     * Genera y descarga un archivo Excel con el listado de reservas en un rango de fechas.
     * Incluye: N° reserva, fecha, embarcación, titular, cédula, total pasajeros, listado de pasajeros, estado.
     *
     * @authenticated
     *
     * @queryParam desde string required Fecha de inicio del rango (YYYY-MM-DD). Example: 2026-05-01
     * @queryParam hasta string required Fecha de fin del rango (YYYY-MM-DD). Example: 2026-05-31
     * @queryParam embarcacion_id integer Filtrar por embarcación específica. Example: 1
     *
     * @response 200 scenario="Éxito" <<binary>> Archivo Excel (.xlsx) con listado de reservas.
     * @response 422 scenario="Fechas inválidas" {
     *   "message": "Error de validación",
     *   "errors": {"hasta": ["The hasta field must be a date after or equal to desde."]}
     * }
     */
    public function excelReservas(Request $request)
    {
        $request->validate([
            'desde'          => 'required|date',
            'hasta'          => 'required|date|after_or_equal:desde',
            'embarcacion_id' => 'nullable|exists:embarcaciones,id',
        ]);

        $filename = 'reservas-' . $request->desde . '-al-' . $request->hasta . '.xlsx';

        return Excel::download(
            new ReservasExport($request->desde, $request->hasta, $request->embarcacion_id),
            $filename
        );
    }

    /**
     * PDF — manifiesto de embarque
     *
     * Genera y retorna el manifiesto oficial de embarque en PDF para una fecha específica.
     * Incluye logo institucional, listado detallado de reservas con pasajeros, y totales.
     * Útil para el operador en el puerto como documento de control.
     *
     * @authenticated
     *
     * @queryParam fecha string required Fecha de embarque (YYYY-MM-DD). Example: 2026-04-13
     * @queryParam embarcacion_id integer Filtrar por embarcación específica. Si no se especifica, incluye todas. Example: 1
     *
     * @response 200 scenario="Éxito" <<binary>> Archivo PDF con el manifiesto de embarque.
     * @response 422 scenario="Validación" {
     *   "message": "Error de validación",
     *   "errors": {"fecha": ["The fecha field is required."]}
     * }
     */
    public function pdfManifiesto(Request $request)
    {
        $request->validate([
            'fecha'          => 'required|date',
            'embarcacion_id' => 'nullable|exists:embarcaciones,id',
        ]);

        $reservas = Reserva::with('pasajeros', 'embarcacion', 'user', 'boleto')
            ->whereDate('fecha', $request->fecha)
            ->whereNotIn('estado', ['cancelada'])
            ->when($request->embarcacion_id, fn($q) =>
                $q->where('embarcacion_id', $request->embarcacion_id)
            )
            ->orderBy('embarcacion_id')
            ->get();

        $embarcacionNombre = null;
        if ($request->embarcacion_id) {
            $embarcacionNombre = Embarcacion::find($request->embarcacion_id)?->nombre;
        }

        $logoPath   = public_path('images/logo-uleam-nuevo.png');
        $logoBase64 = file_exists($logoPath)
            ? base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('pdf.manifiesto', [
            'reservas'    => $reservas,
            'fecha'       => $request->fecha,
            'embarcacion' => $embarcacionNombre,
            'logoBase64'  => $logoBase64,
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'sans-serif',
        ]);

        $filename = 'manifiesto-' . $request->fecha . '.pdf';

        return $pdf->stream($filename);
    }
}