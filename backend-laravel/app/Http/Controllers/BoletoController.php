<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Http\Resources\BoletoResource;
use App\Http\Resources\PasajeroResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * @group Boletos
 *
 * Endpoints para consulta, descarga y validación de boletos de embarque.
 */
class BoletoController extends Controller
{
    /**
     * Ver boleto
     *
     * Retorna el detalle de un boleto. Solo el dueño de la reserva o un admin puede verlo.
     *
     * @authenticated
     *
     * @urlParam id integer required ID del boleto. Example: 1
     *
     * @response 200 scenario="Éxito" {
     *   "data": {
     *     "id": 1,
     *     "codigo_qr": "01KP2EZ650JKEFGPEZHGQWM5PD",
     *     "estado": "valido",
     *     "pdf_url": null,
     *     "usado_en": null
     *   }
     * }
     * @response 403 {"error": "No autorizado"}
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function show($id)
    {
        $boleto = Boleto::with([
            'reserva.embarcacion',
            'reserva.pasajeros',
            'reserva.user',
        ])->findOrFail($id);

        if ($boleto->reserva->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        if ($boleto->estaVencido()) {
            $boleto->marcarVencido();
        }

        return new BoletoResource($boleto);
    }

    /**
     * Descargar PDF del boleto
     *
     * Genera y retorna el PDF del boleto de embarque con código QR integrado.
     * Solo el dueño de la reserva o un admin puede descargarlo.
     * El PDF se guarda en storage para futuras descargas.
     *
     * @authenticated
     *
     * @urlParam id integer required ID del boleto. Example: 1
     *
     * @response 200 scenario="Éxito" <<binary>> El archivo PDF del boleto.
     * @response 403 {"error": "No autorizado"}
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function generarPDF($id)
    {
        $boleto = Boleto::with([
            'reserva.embarcacion',
            'reserva.pasajeros',
            'reserva.user',
        ])->findOrFail($id);

        if ($boleto->reserva->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $qrSvg = QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate($boleto->codigo_qr);

        $qrBase64 = base64_encode($qrSvg);

        $pdf = Pdf::loadView('pdf.ticket', [
            'boleto'      => $boleto,
            'reserva'     => $boleto->reserva,
            'embarcacion' => $boleto->reserva->embarcacion,
            'pasajeros'   => $boleto->reserva->pasajeros,
            'usuario'     => $boleto->reserva->user,
            'logoBase64'  => base64_encode(file_get_contents(public_path('images/logo-uleam-nuevo.png'))),
            'qrBase64'    => $qrBase64,
        ])
        ->setPaper([0, 0, 396, 612], 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'sans-serif',
        ]);

        $filename = 'boletos/boleto-' . $boleto->codigo_qr . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        if (!$boleto->pdf_url) {
            $boleto->update(['pdf_url' => Storage::url($filename)]);
        }

        return $pdf->stream('boleto-' . $boleto->codigo_qr . '.pdf');
    }

    /**
     * Validar boleto QR
     *
     * Valida un boleto escaneando su código QR en el puerto de embarque.
     * Requiere rol operador o admin.
     * Verifica que el boleto sea válido y no haya sido usado.
     * El embarque habilita desde 3 horas antes de la hora de salida hasta 30 minutos después
     * (esto ya cubre el caso de reservas que cruzan la medianoche).
     * No marca el boleto como usado — eso ocurre al confirmar el checklist en `embarcar()`.
     *
     * @authenticated
     *
     * @urlParam codigo string required Código QR del boleto. Example: 01KP2EZ650JKEFGPEZHGQWM5PD
     *
     * @response 200 scenario="Válido" {
     *   "valido": true,
     *   "message": "Boleto válido",
     *   "boleto_id": 1,
     *   "reserva_id": 1,
     *   "embarcacion": "Lancha Uleam I",
     *   "capacidad": 25,
     *   "pasajeros": [
     *     {"id": 1, "nombre": "Juan Pérez", "cedula": "0950638675", "tipo": "estudiante"}
     *   ],
     *   "total": 2
     * }
     * @response 400 scenario="Ya usado" {
     *   "valido": false,
     *   "error": "Boleto ya fue usado",
     *   "usado_en": "13/04/2026 21:55"
     * }
     * @response 400 scenario="Cancelado" {
     *   "valido": false,
     *   "error": "Boleto cancelado"
     * }
     * @response 400 scenario="Demasiado temprano" {
     *   "valido": false,
     *   "error": "Aún no es hora de abordar. El embarque habilita desde las 09:00"
     * }
     * @response 400 scenario="Ventana expirada" {
     *   "valido": false,
     *   "error": "El boleto expiró. La ventana de embarque ya cerró."
     * }
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function validar($codigo)
    {
        $boleto = Boleto::with([
            'reserva.embarcacion',
            'reserva.pasajeros',
        ])->where('codigo_qr', $codigo)->first();

        if (!$boleto) {
            return response()->json([
                'valido' => false,
                'error'  => 'Código de boleto no encontrado. Verifica e intenta nuevamente.',
            ], 404);
        }

        if ($boleto->estado === 'usado') {
            return response()->json([
                'valido'   => false,
                'error'    => 'Boleto ya fue usado',
                'usado_en' => $boleto->usado_en?->format('d/m/Y H:i'),
            ], 400);
        }

        if ($boleto->estado === 'cancelado') {
            return response()->json([
                'valido' => false,
                'error'  => 'Boleto cancelado',
            ], 400);
        }

        if ($boleto->estado === 'vencido') {
            return response()->json([
                'valido' => false,
                'error'  => 'El boleto expiró sin ser utilizado',
            ], 400);
        }

        $salida        = \Carbon\Carbon::parse($boleto->reserva->fecha->toDateString() . ' ' . $boleto->reserva->hora_inicio);
        $ventanaInicio = $salida->copy()->subHours(3);
        $ventanaFin    = $salida->copy()->addMinutes(30);

        if (now()->lt($ventanaInicio)) {
            return response()->json([
                'valido' => false,
                'error'  => "Aún no es hora de abordar. El embarque habilita desde las {$ventanaInicio->format('H:i')}",
            ], 400);
        }

        if (now()->gt($ventanaFin)) {
            $boleto->marcarVencido();

            return response()->json([
                'valido' => false,
                'error'  => 'El boleto expiró. La ventana de embarque ya cerró.',
            ], 400);
        }

        return response()->json([
            'valido'      => true,
            'message'     => 'Boleto válido',
            'boleto_id'   => $boleto->id,
            'reserva_id'  => $boleto->reserva->id,
            'embarcacion' => $boleto->reserva->embarcacion->nombre,
            'capacidad'   => $boleto->reserva->embarcacion->capacidad,
            'pasajeros'   => PasajeroResource::collection($boleto->reserva->pasajeros),
            'total'       => $boleto->reserva->total_personas,
        ]);
    }

    /**
     * Confirmar embarque (checklist de pasajeros)
     *
     * Confirma el embarque de una reserva ya validada, marcando el boleto como usado,
     * la reserva como completada y registrando qué pasajeros abordaron realmente.
     * Requiere rol operador o admin.
     *
     * @authenticated
     *
     * @urlParam id integer required ID del boleto. Example: 1
     * @bodyParam pasajeros_ausentes integer[] IDs de los pasajeros que NO abordaron. Example: [3]
     *
     * @response 200 {"message": "Embarque registrado correctamente", "total": 3, "presentes": 2}
     * @response 400 scenario="Ya procesado" {"error": "Este boleto ya fue procesado o no es válido"}
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function embarcar(Request $request, $id)
    {
        $request->validate([
            'pasajeros_ausentes'   => 'array',
            'pasajeros_ausentes.*' => 'integer|exists:pasajeros,id',
        ]);

        $boleto = Boleto::with('reserva.pasajeros')->findOrFail($id);

        if ($boleto->estado !== 'valido') {
            return response()->json(['error' => 'Este boleto ya fue procesado o no es válido'], 400);
        }

        $ausentes = collect($request->input('pasajeros_ausentes', []));

        return DB::transaction(function () use ($boleto, $ausentes) {
            foreach ($boleto->reserva->pasajeros as $p) {
                $p->update(['presente' => !$ausentes->contains($p->id)]);
            }

            $boleto->update([
                'estado'   => 'usado',
                'usado_en' => now(),
            ]);

            $boleto->reserva->update(['estado' => 'completada']);

            return response()->json([
                'message'   => 'Embarque registrado correctamente',
                'total'     => $boleto->reserva->pasajeros->count(),
                'presentes' => $boleto->reserva->pasajeros->count() - $ausentes->count(),
            ]);
        });
    }
}