<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\Viaje;
use App\Http\Resources\PasajeroResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * @group Control Operativo del Viaje
 *
 * Ciclo operativo completo de un viaje a partir del escaneo del QR de una reserva:
 * checklist de embarque, evidencia fotográfica, confirmación de salida/llegada,
 * cierre del viaje y reporte final. Requiere rol operador o admin.
 */
class ViajeController extends Controller
{
    private array $eager = [
        'reserva.embarcacion',
        'reserva.user',
        'reserva.pasajeros',
        'embarcacion',
        'operador',
        'evidencias',
    ];

    /**
     * Escanear QR de reserva
     *
     * Valida la reserva (existe, aprobada, dentro de la ventana de embarque, no finalizada)
     * y abre (creando si no existe) el registro operativo del viaje.
     *
     * @authenticated
     *
     * @urlParam codigo string required Código QR del boleto. Example: 01KPSRM0D62MP2HAXD0NE04358
     *
     * @response 200 scenario="Éxito" {"viaje": {"id": 1, "estado": "abordando"}}
     * @response 400 scenario="No aprobada" {"error": "La reserva no está aprobada"}
     * @response 400 scenario="Finalizado" {"error": "Este viaje ya fue finalizado"}
     * @response 400 scenario="Fuera de ventana" {"error": "Aún no es hora de abordar..."}
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function escanear($codigo)
    {
        $boleto = Boleto::with('reserva.viaje')->where('codigo_qr', $codigo)->first();

        if (!$boleto) {
            return response()->json(['error' => 'Código de boleto no encontrado. Verifica e intenta nuevamente.'], 404);
        }

        $reserva = $boleto->reserva;

        if ($reserva->estado !== 'confirmada') {
            return response()->json(['error' => 'La reserva no está aprobada'], 400);
        }

        $viaje = $reserva->viaje;

        if ($viaje && $viaje->estado === 'finalizado') {
            return response()->json(['error' => 'Este viaje ya fue finalizado'], 400);
        }

        if (!$viaje) {
            $horaInicio    = Carbon::parse($reserva->fecha->toDateString() . ' ' . $reserva->hora_inicio);
            $ventanaInicio = $horaInicio->copy()->subHours(3);
            $ventanaFin    = $horaInicio->copy()->addMinutes(30);

            if (now()->lt($ventanaInicio)) {
                return response()->json([
                    'error' => "Aún no es hora de abordar. El embarque habilita desde las {$ventanaInicio->format('H:i')}",
                ], 400);
            }

            if (now()->gt($ventanaFin)) {
                return response()->json([
                    'error' => 'La ventana de embarque ya cerró.',
                ], 400);
            }

            $horaFin = Carbon::parse($reserva->fecha->toDateString() . ' ' . $reserva->hora_fin);
            if ($horaFin->lt($horaInicio)) {
                $horaFin->addDay();
            }

            $viaje = Viaje::create([
                'reserva_id'              => $reserva->id,
                'embarcacion_id'          => $reserva->embarcacion_id,
                'operador_id'             => auth()->id(),
                'estado'                  => 'abordando',
                'hora_programada_salida'  => $horaInicio,
                'hora_programada_llegada' => $horaFin,
            ]);
        }

        return response()->json($this->payload($viaje->fresh($this->eager)));
    }

    /**
     * Ver viaje
     *
     * Retorna el detalle completo del viaje operativo (para reabrir la pantalla sin re-escanear).
     *
     * @authenticated
     * @urlParam id integer required ID del viaje. Example: 1
     */
    public function show($id)
    {
        $viaje = Viaje::with($this->eager)->findOrFail($id);

        return response()->json($this->payload($viaje));
    }

    /**
     * Viajes activos
     *
     * Lista los viajes en curso (ABORDANDO o EN_CURSO). Un admin ve todos los
     * viajes activos del sistema (para supervisión); un operador solo ve los
     * que él mismo escaneó/inició. Para forzar "solo los míos" incluso como
     * admin (usado por el aviso de "¿continuar viaje?" al abrir la app, que
     * debe ser siempre personal — el responsable es quien escaneó el QR, no
     * cualquier admin/operador que abra la sesión) se puede pasar `?propio=1`.
     *
     * @authenticated
     *
     * @queryParam propio boolean Si es 1, fuerza mostrar solo los viajes escaneados por el usuario autenticado, aunque sea admin. Example: 1
     *
     * @response 200 scenario="Éxito" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "estado": "en_curso",
     *       "embarcacion": "Lancha Uleam I",
     *       "responsable": "Juan Pérez",
     *       "operador": "Carlos Operador",
     *       "fecha": "2026-07-09",
     *       "hora_programada_salida": "2026-07-09 08:00",
     *       "total_personas": 3
     *     }
     *   ]
     * }
     */
    public function activos(Request $request)
    {
        $query = Viaje::with(['reserva.embarcacion', 'reserva.user', 'operador'])
            ->whereIn('estado', ['abordando', 'en_curso']);

        // El operador siempre ve solo lo suyo. El admin ve todo, salvo que
        // pida explícitamente "?propio=1" (usado por el diálogo de retomar
        // viaje del splash, que debe ser siempre personal).
        if ($request->boolean('propio') || !auth()->user()->hasRole('admin')) {
            $query->where('operador_id', auth()->id());
        }

        $viajes = $query->orderBy('hora_programada_salida')->get();

        return response()->json([
            'data' => $viajes->map(fn (Viaje $v) => [
                'id'                      => $v->id,
                'estado'                  => $v->estado,
                'embarcacion'             => $v->reserva->embarcacion->nombre,
                'responsable'             => $v->reserva->user->name,
                'operador'                => $v->operador->name,
                'fecha'                   => $v->reserva->fecha->format('Y-m-d'),
                'hora_programada_salida'  => $v->hora_programada_salida->format('Y-m-d H:i'),
                'total_personas'          => $v->reserva->total_personas,
            ]),
        ]);
    }

    /**
     * Checklist de embarque
     *
     * Marca qué pasajeros abordaron, con hora automática y observaciones opcionales.
     * Solo editable mientras el viaje está en estado ABORDANDO.
     *
     * @authenticated
     * @urlParam id integer required ID del viaje. Example: 1
     * @bodyParam pasajeros array required Lista de pasajeros a marcar.
     * @bodyParam pasajeros[].id integer required ID del pasajero. Example: 1
     * @bodyParam pasajeros[].abordo boolean required Si abordó o no. Example: true
     * @bodyParam pasajeros[].observaciones string Observaciones opcionales. Example: Llegó con retraso
     */
    public function checklistEmbarque(Request $request, $id)
    {
        $viaje = Viaje::with('reserva.pasajeros')->findOrFail($id);

        if ($viaje->estado !== 'abordando') {
            return response()->json([
                'error' => 'El checklist de embarque solo se puede editar mientras el viaje está en estado de abordaje',
            ], 400);
        }

        $request->validate([
            'pasajeros'                  => 'required|array|min:1',
            'pasajeros.*.id'             => 'required|integer|exists:pasajeros,id',
            'pasajeros.*.abordo'         => 'required|boolean',
            'pasajeros.*.observaciones'  => 'nullable|string',
        ]);

        foreach ($request->pasajeros as $p) {
            $pasajero = $viaje->reserva->pasajeros->firstWhere('id', $p['id']);
            if (!$pasajero) {
                continue;
            }

            $pasajero->update([
                'presente'               => $p['abordo'],
                'observaciones_embarque' => $p['observaciones'] ?? null,
                'hora_abordaje'          => $p['abordo'] ? ($pasajero->hora_abordaje ?? now()) : null,
            ]);
        }

        $payload = $this->payload($viaje->fresh($this->eager));
        $payload['message'] = 'Checklist de embarque actualizado';

        return response()->json($payload);
    }

    /**
     * Checklist de llegada
     *
     * Marca qué pasajeros llegaron, con hora automática y observaciones opcionales.
     * Solo editable mientras el viaje está EN_CURSO.
     *
     * @authenticated
     * @urlParam id integer required ID del viaje. Example: 1
     * @bodyParam pasajeros array required Lista de pasajeros a marcar.
     * @bodyParam pasajeros[].id integer required ID del pasajero. Example: 1
     * @bodyParam pasajeros[].llego boolean required Si llegó o no. Example: true
     * @bodyParam pasajeros[].observaciones string Observaciones opcionales.
     */
    public function checklistLlegada(Request $request, $id)
    {
        $viaje = Viaje::with('reserva.pasajeros')->findOrFail($id);

        if ($viaje->estado !== 'en_curso') {
            return response()->json([
                'error' => 'El checklist de llegada solo se puede editar mientras el viaje está en curso',
            ], 400);
        }

        $request->validate([
            'pasajeros'                  => 'required|array|min:1',
            'pasajeros.*.id'             => 'required|integer|exists:pasajeros,id',
            'pasajeros.*.llego'          => 'required|boolean',
            'pasajeros.*.observaciones'  => 'nullable|string',
        ]);

        foreach ($request->pasajeros as $p) {
            $pasajero = $viaje->reserva->pasajeros->firstWhere('id', $p['id']);
            if (!$pasajero) {
                continue;
            }

            $pasajero->update([
                'llego'                  => $p['llego'],
                'observaciones_llegada'  => $p['observaciones'] ?? null,
                'hora_llegada'           => $p['llego'] ? ($pasajero->hora_llegada ?? now()) : null,
            ]);
        }

        $payload = $this->payload($viaje->fresh($this->eager));
        $payload['message'] = 'Checklist de llegada actualizado';

        return response()->json($payload);
    }

    /**
     * Subir evidencia fotográfica
     *
     * Sube una o varias fotos como evidencia de salida o llegada del viaje.
     *
     * @authenticated
     * @urlParam id integer required ID del viaje. Example: 1
     * @bodyParam tipo string required salida o llegada. Example: salida
     * @bodyParam fotos file[] required Una o varias imágenes.
     */
    public function subirEvidencia(Request $request, $id)
    {
        $viaje = Viaje::findOrFail($id);

        $request->validate([
            'tipo'     => 'required|in:salida,llegada',
            'fotos'    => 'required|array|min:1',
            'fotos.*'  => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->tipo === 'salida' && $viaje->estado !== 'abordando') {
            return response()->json([
                'error' => 'Solo se puede subir evidencia de salida mientras el viaje está en estado de abordaje',
            ], 400);
        }

        if ($request->tipo === 'llegada' && $viaje->estado !== 'en_curso') {
            return response()->json([
                'error' => 'Solo se puede subir evidencia de llegada mientras el viaje está en curso',
            ], 400);
        }

        foreach ($request->file('fotos') as $foto) {
            $ruta = $foto->store('viajes/evidencias', 'public');
            $viaje->evidencias()->create([
                'ruta'    => $ruta,
                'tipo'    => $request->tipo,
                'user_id' => auth()->id(),
            ]);
        }

        $payload = $this->payload($viaje->fresh($this->eager));
        $payload['message'] = 'Evidencia subida correctamente';

        return response()->json($payload, 201);
    }

    public function eliminarEvidencia($id, $evidencia)
    {
        $viaje = Viaje::findOrFail($id);
        $evidenciaModel = $viaje->evidencias()->findOrFail($evidencia);

        if ($evidenciaModel->tipo === 'salida' && $viaje->estado !== 'abordando') {
            return response()->json([
                'error' => 'Solo se puede eliminar evidencia de salida mientras el viaje está en estado de abordaje',
            ], 400);
        }

        if ($evidenciaModel->tipo === 'llegada' && $viaje->estado !== 'en_curso') {
            return response()->json([
                'error' => 'Solo se puede eliminar evidencia de llegada mientras el viaje está en curso',
            ], 400);
        }

        Storage::disk('public')->delete($evidenciaModel->ruta);
        $evidenciaModel->delete();

        $payload = $this->payload($viaje->fresh($this->eager));
        $payload['message'] = 'Evidencia eliminada correctamente';

        return response()->json($payload);
    }

    /**
     * Confirmar salida
     *
     * Registra la hora real de salida, marca el boleto como usado y cambia el
     * viaje a EN_CURSO. Requiere al menos una foto de evidencia de salida.
     *
     * @authenticated
     * @urlParam id integer required ID del viaje. Example: 1
     * @response 400 scenario="Sin evidencia" {"error": "Debes subir al menos una foto de evidencia de salida"}
     */
    public function iniciar($id)
    {
        $viaje = Viaje::with('reserva.boleto', 'reserva.pasajeros')->findOrFail($id);

        if ($viaje->estado !== 'abordando') {
            return response()->json(['error' => 'El viaje ya fue iniciado'], 400);
        }

        if (!$this->checklistEmbarqueCompleto($viaje->reserva)) {
            return response()->json(['error' => 'Debes completar el checklist de embarque antes de confirmar la salida'], 400);
        }

        if (!$viaje->evidencias()->where('tipo', 'salida')->exists()) {
            return response()->json(['error' => 'Debes subir al menos una foto de evidencia de salida'], 400);
        }

        return DB::transaction(function () use ($viaje) {
            $viaje->update([
                'estado'            => 'en_curso',
                'hora_real_salida'  => now(),
            ]);

            $viaje->reserva->boleto?->update([
                'estado'   => 'usado',
                'usado_en' => now(),
            ]);

            $payload = $this->payload($viaje->fresh($this->eager));
            $payload['message'] = 'Salida confirmada';

            return response()->json($payload);
        });
    }

    /**
     * Registrar llegada real
     *
     * Registra la hora real de llegada del viaje. Requiere al menos una foto
     * de evidencia de llegada.
     *
     * @authenticated
     * @urlParam id integer required ID del viaje. Example: 1
     * @response 400 scenario="Sin evidencia" {"error": "Debes subir al menos una foto de evidencia de llegada"}
     */
    public function registrarLlegada($id)
    {
        $viaje = Viaje::with('reserva.pasajeros')->findOrFail($id);

        if ($viaje->estado !== 'en_curso') {
            return response()->json(['error' => 'El viaje no está en curso'], 400);
        }

        if (!$this->checklistLlegadaCompleto($viaje->reserva)) {
            return response()->json(['error' => 'Debes completar el checklist de llegada antes de confirmar la llegada'], 400);
        }

        if (!$viaje->evidencias()->where('tipo', 'llegada')->exists()) {
            return response()->json(['error' => 'Debes subir al menos una foto de evidencia de llegada'], 400);
        }

        $viaje->update(['hora_real_llegada' => now()]);

        $payload = $this->payload($viaje->fresh($this->eager));
        $payload['message'] = 'Llegada registrada';

        return response()->json($payload);
    }

    /**
     * Finalizar viaje
     *
     * Cierra el viaje. Solo se permite si todos los pasajeros que abordaron fueron
     * confirmados como llegados; si falta alguno, únicamente un administrador puede
     * cerrarlo, y debe indicar `observaciones_cierre` justificando el cierre.
     *
     * @authenticated
     * @urlParam id integer required ID del viaje. Example: 1
     * @bodyParam observaciones_cierre string Requerido solo si hay pasajeros pendientes por confirmar.
     * @response 403 scenario="Pendientes, no admin" {"error": "Solo un administrador puede cerrar el viaje con pasajeros pendientes por confirmar"}
     */
    public function finalizar(Request $request, $id)
    {
        $viaje = Viaje::with('reserva.pasajeros')->findOrFail($id);

        if ($viaje->estado !== 'en_curso') {
            return response()->json(['error' => 'El viaje debe estar en curso para finalizarlo'], 400);
        }

        $pendientes = $viaje->reserva->pasajeros->filter(
            fn($p) => $p->presente === true && $p->llego !== true
        );

        if ($pendientes->isNotEmpty()) {
            if (!auth()->user()->hasRole('admin')) {
                return response()->json([
                    'error' => 'Solo un administrador puede cerrar el viaje con pasajeros pendientes por confirmar',
                ], 403);
            }

            $request->validate([
                'observaciones_cierre' => 'required|string',
            ]);
        } else {
            $request->validate([
                'observaciones_cierre' => 'nullable|string',
            ]);
        }

        return DB::transaction(function () use ($viaje, $request) {
            $viaje->update([
                'estado'               => 'finalizado',
                'usuario_cierre_id'    => auth()->id(),
                'fecha_finalizacion'   => now(),
                'observaciones_cierre' => $request->observaciones_cierre,
            ]);

            $viaje->reserva->update(['estado' => 'completada']);

            $payload = $this->payload($viaje->fresh($this->eager));
            $payload['message'] = 'Viaje finalizado correctamente';

            return response()->json($payload);
        });
    }

    /**
     * Reporte final del viaje (PDF)
     *
     * Genera el reporte final con toda la información del viaje: reserva, embarcación,
     * responsable, operador, horas programadas/reales, duración, pasajeros, checklists,
     * observaciones, evidencia fotográfica y estado final.
     *
     * @authenticated
     * @urlParam id integer required ID del viaje. Example: 1
     * @response 200 <<binary>> Archivo PDF con el reporte final del viaje.
     */
    public function reportePdf($id)
    {
        $viaje = Viaje::with([
            'reserva.embarcacion',
            'reserva.user',
            'reserva.pasajeros',
            'operador',
            'usuarioCierre',
            'evidencias',
        ])->findOrFail($id);

        $logoPath   = public_path('images/logo-uleam-nuevo.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        $fotosSalida  = $viaje->evidencias->where('tipo', 'salida')
            ->map(fn($e) => $this->imagenBase64($e->ruta))->filter()->values();
        $fotosLlegada = $viaje->evidencias->where('tipo', 'llegada')
            ->map(fn($e) => $this->imagenBase64($e->ruta))->filter()->values();

        $duracion = null;
        if ($viaje->hora_real_salida && $viaje->hora_real_llegada) {
            $minutos  = $viaje->hora_real_salida->diffInMinutes($viaje->hora_real_llegada);
            $duracion = sprintf('%dh %02dm', intdiv($minutos, 60), $minutos % 60);
        }

        $pdf = Pdf::loadView('pdf.viaje-reporte', [
            'viaje'        => $viaje,
            'reserva'      => $viaje->reserva,
            'embarcacion'  => $viaje->reserva->embarcacion,
            'pasajeros'    => $viaje->reserva->pasajeros,
            'duracion'     => $duracion,
            'fotosSalida'  => $fotosSalida,
            'fotosLlegada' => $fotosLlegada,
            'logoBase64'   => $logoBase64,
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'defaultFont'          => 'DejaVu Sans',
        ]);

        return $pdf->stream('reporte-viaje-' . $viaje->id . '.pdf');
    }

    private function imagenBase64(string $ruta): ?string
    {
        $path = storage_path('app/public/' . $ruta);
        if (!file_exists($path)) {
            return null;
        }

        return 'data:image/jpeg;base64,' . base64_encode($this->redimensionarParaPdf($path));
    }

    /**
     * Las fotos de evidencia se suben tal cual salen de la cámara del celular
     * (hasta 5MB cada una, sin comprimir — ver subirEvidencia()). Meterlas
     * completas en base64 dentro del HTML que renderiza DomPDF es lo que
     * hacía que generar el reporte tardara más de los 15s de timeout del
     * cliente en viajes con varias fotos — DomPDF es PHP puro, no tiene
     * aceleración de hardware, y escala muy mal con imágenes grandes.
     * Reescalarlas a un ancho razonable para un reporte (no necesitan
     * resolución de cámara) resuelve la lentitud de raíz.
     */
    private function redimensionarParaPdf(string $path, int $anchoMax = 900): string
    {
        $info = @getimagesize($path);
        if (!$info) {
            return file_get_contents($path);
        }

        [$ancho, $alto] = $info;
        $origen = match ($info['mime']) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png'  => @imagecreatefrompng($path),
            'image/webp' => @imagecreatefromwebp($path),
            default      => null,
        };

        if (!$origen) {
            return file_get_contents($path);
        }

        if ($ancho > $anchoMax) {
            $altoNuevo = intdiv($alto * $anchoMax, $ancho);
            $destino   = imagecreatetruecolor($anchoMax, $altoNuevo);
            imagecopyresampled($destino, $origen, 0, 0, 0, 0, $anchoMax, $altoNuevo, $ancho, $alto);
            imagedestroy($origen);
            $origen = $destino;
        }

        ob_start();
        imagejpeg($origen, null, 75);
        $contenido = ob_get_clean();
        imagedestroy($origen);

        return $contenido;
    }

    /**
     * El checklist de embarque se considera completo cuando cada pasajero
     * quedó explícitamente marcado: o bien abordó (tiene hora_abordaje) o
     * bien fue marcado como ausente (presente = false). Antes de guardar el
     * checklist por primera vez, presente es true por defecto para todos y
     * hora_abordaje es null, así que la condición da false — que es lo que
     * queremos: sin checklist guardado, no se puede confirmar la salida.
     */
    private function checklistEmbarqueCompleto($reserva): bool
    {
        return $reserva->pasajeros->every(
            fn($p) => $p->presente === false || $p->hora_abordaje !== null
        );
    }

    /**
     * Mismo criterio que checklistEmbarqueCompleto pero solo sobre los
     * pasajeros que sí abordaron (los únicos relevantes para el checklist
     * de llegada).
     */
    private function checklistLlegadaCompleto($reserva): bool
    {
        return $reserva->pasajeros
            ->where('presente', true)
            ->every(fn($p) => $p->llego !== null);
    }

    private function payload(Viaje $viaje): array
    {
        $reserva = $viaje->reserva;

        return [
            'viaje' => [
                'id'                       => $viaje->id,
                'estado'                   => $viaje->estado,
                'hora_programada_salida'   => $viaje->hora_programada_salida->format('Y-m-d H:i'),
                'hora_programada_llegada'  => $viaje->hora_programada_llegada->format('Y-m-d H:i'),
                'hora_real_salida'         => $viaje->hora_real_salida?->format('Y-m-d H:i'),
                'hora_real_llegada'        => $viaje->hora_real_llegada?->format('Y-m-d H:i'),
                'fecha_finalizacion'       => $viaje->fecha_finalizacion?->format('Y-m-d H:i'),
                'observaciones_cierre'     => $viaje->observaciones_cierre,
                'checklist_embarque_completo' => $this->checklistEmbarqueCompleto($reserva),
                'checklist_llegada_completo'  => $this->checklistLlegadaCompleto($reserva),
            ],
            'reserva' => [
                'id'             => $reserva->id,
                'fecha'          => $reserva->fecha->format('Y-m-d'),
                'total_personas' => $reserva->total_personas,
            ],
            'embarcacion' => [
                'id'        => $viaje->embarcacion->id,
                'nombre'    => $viaje->embarcacion->nombre,
                'capacidad' => $viaje->embarcacion->capacidad,
            ],
            'responsable' => [
                'nombre'   => $reserva->user->name,
                'email'    => $reserva->user->email,
                'telefono' => $reserva->user->telefono,
            ],
            'operador' => [
                'nombre' => $viaje->operador->name,
            ],
            'pasajeros'  => PasajeroResource::collection($reserva->pasajeros),
            'evidencias' => $viaje->evidencias->map(fn($e) => [
                'id'   => $e->id,
                'tipo' => $e->tipo,
                'url'  => asset('media/' . $e->ruta),
            ]),
        ];
    }
}
