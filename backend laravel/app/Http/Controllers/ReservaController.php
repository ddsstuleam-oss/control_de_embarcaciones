<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Embarcacion;
use App\Models\Boleto;
use App\Http\Resources\ReservaResource;
use App\Mail\ReservaConfirmada;
use App\Mail\ReservaPendiente;
use App\Mail\ReservaRechazada;
use App\Mail\ReservaCancelada;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * @group Reservas
 *
 * Endpoints para gestión de reservas de embarcaciones.
 */
class ReservaController extends Controller
{
    /**
     * Listar mis reservas
     *
     * Retorna todas las reservas del usuario autenticado.
     *
     * @authenticated
     *
     * @response 200 scenario="Éxito" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "fecha": "2026-05-10",
     *       "total_personas": 2,
     *       "estado": "confirmada",
     *       "embarcacion": {"id": 1, "nombre": "Lancha Uleam I"},
     *       "boleto": {"id": 1, "codigo_qr": "01KP2EZ650JKEFGPEZHGQWM5PD", "estado": "valido"}
     *     }
     *   ]
     * }
     */
    public function index()
    {
        $reservas = Reserva::with('pasajeros', 'embarcacion', 'boleto', 'decididoPor', 'canceladoPor')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return ReservaResource::collection($reservas);
    }

    /**
     * Ver una reserva
     *
     * Retorna el detalle completo de una reserva del usuario autenticado.
     *
     * @authenticated
     *
     * @urlParam id integer required ID de la reserva. Example: 1
     *
     * @response 200 scenario="Éxito" {
     *   "data": {
     *     "id": 1,
     *     "fecha": "2026-05-10",
     *     "total_personas": 2,
     *     "estado": "confirmada",
     *     "embarcacion": {"id": 1, "nombre": "Lancha Uleam I"},
     *     "pasajeros": [
     *       {"id": 1, "nombre": "Juan Pérez", "cedula": "0950638675", "tipo": "estudiante"}
     *     ],
     *     "boleto": {"codigo_qr": "01KP2EZ650JKEFGPEZHGQWM5PD", "estado": "valido"}
     *   }
     * }
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function show($id)
    {
        $reserva = Reserva::with('pasajeros', 'embarcacion', 'boleto', 'decididoPor', 'canceladoPor')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        if ($reserva->estaVencidaPendiente()) {
            $reserva->marcarVencida();
        }

        return new ReservaResource($reserva);
    }

    /**
     * Crear reserva
     *
     * Crea una nueva reserva con pasajeros y boleto QR. Envía email de confirmación con PDF adjunto.
     * Usa lock de base de datos para evitar condiciones de carrera en los cupos.
     * La fecha y horario están siempre disponibles y pueden tener varias reservas
     * pendientes simultáneas: solo se bloquean cuando un administrador autoriza una de ellas.
     *
     * @authenticated
     *
     * @bodyParam embarcacion_id integer required ID de la embarcación. Example: 1
     * @bodyParam fecha string required Fecha del viaje (YYYY-MM-DD), debe ser hoy o futura. Example: 2026-05-10
     * @bodyParam total_personas integer required Número total de pasajeros (1-100). Example: 2
     * @bodyParam pasajeros array required Lista de pasajeros, debe coincidir con total_personas.
     * @bodyParam pasajeros[].nombre string required Nombre completo del pasajero. Example: Juan Pérez
     * @bodyParam pasajeros[].cedula string required Cédula del pasajero. Example: 0950638675
     * @bodyParam pasajeros[].tipo string Tipo de pasajero: estudiante, docente, administrativo, externo. Example: estudiante
     * @bodyParam pasajeros[].carrera string Carrera (solo estudiantes). Example: Medicina Veterinaria
     * @bodyParam pasajeros[].facultad string Facultad a la que pertenece la carrera. Example: Facultad Ciencias de la Salud
     * @bodyParam pasajeros[].telefono string Teléfono de contacto. Example: 0991234567
     * @bodyParam pasajeros[].email string Correo del pasajero. Example: juan@example.com
     *
     * @response 201 scenario="Reserva creada" {
     *   "message": "Reserva creada exitosamente",
     *   "reserva": {
     *     "id": 1,
     *     "fecha": "2026-05-10",
     *     "total_personas": 2,
     *     "estado": "confirmada",
     *     "boleto": {"codigo_qr": "01KP2EZ650JKEFGPEZHGQWM5PD", "estado": "valido"}
     *   }
     * }
     * @response 400 scenario="Sin cupos" {"error": "No hay cupos suficientes", "disponibles": 3, "solicitados": 5}
     * @response 400 scenario="No disponible" {"error": "Embarcación no disponible"}
     * @response 422 scenario="Validación" {"error": "El número de pasajeros no coincide con total_personas"}
     */
public function store(Request $request)
{
    $request->validate([
        'embarcacion_id' => 'required|exists:embarcaciones,id',
        'fecha'          => 'required|date|after_or_equal:today',
        'hora_inicio'    => 'required|date_format:H:i',
        'hora_fin'       => 'required|date_format:H:i|after:hora_inicio',
        'total_personas' => 'required|integer|min:1',
        'pasajeros'      => 'required|array|min:1',
        'pasajeros.*.nombre' => 'required|string',
        'pasajeros.*.cedula' => 'required|digits:10',
        'pasajeros.*.tipo'   => 'required|in:estudiante,docente,administrativo,externo',
        'pasajeros.*.carrera'=> 'nullable|string',
        'pasajeros.*.facultad'=> 'nullable|string',
    ]);

    $embarcacion = Embarcacion::findOrFail($request->embarcacion_id);

    return DB::transaction(function () use ($request, $embarcacion) {

        // Verificar conflicto de horario. Solo las reservas ya confirmadas por
        // un administrador bloquean el horario; puede haber varias reservas
        // pendientes para la misma fecha y hora hasta que una sea autorizada.
        $conflicto = Reserva::where('embarcacion_id', $request->embarcacion_id)
            ->whereDate('fecha', $request->fecha)
            ->where('estado', 'confirmada')
            ->where(function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    // Nueva reserva empieza dentro de una existente
                    $q->where('hora_inicio', '<=', $request->hora_inicio)
                      ->where('hora_fin',    '>',  $request->hora_inicio);
                })->orWhere(function ($q) use ($request) {
                    // Nueva reserva termina dentro de una existente
                    $q->where('hora_inicio', '<',  $request->hora_fin)
                      ->where('hora_fin',    '>=', $request->hora_fin);
                })->orWhere(function ($q) use ($request) {
                    // Nueva reserva engloba una existente
                    $q->where('hora_inicio', '>=', $request->hora_inicio)
                      ->where('hora_fin',    '<=', $request->hora_fin);
                });
            })
            ->lockForUpdate()
            ->exists();

        if ($conflicto) {
            return response()->json([
                'error' => 'La embarcación ya tiene una reserva en ese horario.',
            ], 409);
        }

        // Verificar cupos (solo cuenta contra reservas ya confirmadas)
$reservados = Reserva::where('embarcacion_id', $request->embarcacion_id)
    ->whereDate('fecha', $request->fecha)
    ->where('estado', 'confirmada')
    ->where(function ($q) use ($request) {
        $q->where(function ($q) use ($request) {
            $q->where('hora_inicio', '<',  $request->hora_fin)
              ->where('hora_fin',    '>',  $request->hora_inicio);
        });
    })
    ->lockForUpdate()
    ->get(['total_personas'])
    ->sum('total_personas');

        if ($reservados + $request->total_personas > $embarcacion->capacidad) {
            return response()->json([
                'error' => 'No hay suficientes cupos disponibles.',
            ], 409);
        }

        $reserva = Reserva::create([
            'user_id'        => auth()->id(),
            'embarcacion_id' => $request->embarcacion_id,
            'fecha'          => $request->fecha,
            'hora_inicio'    => $request->hora_inicio,
            'hora_fin'       => $request->hora_fin,
            'total_personas' => $request->total_personas,
            'estado'         => 'pendiente',
        ]);

        foreach ($request->pasajeros as $p) {
            $reserva->pasajeros()->create([
                'nombre'  => $p['nombre'],
                'cedula'  => $p['cedula'],
                'tipo'    => $p['tipo'],
                'carrera' => $p['carrera'] ?? null,
                'facultad'=> $p['facultad'] ?? null,
            ]);
        }

        // El boleto se genera recién cuando el administrador aprueba la reserva

        // Email en background
        Mail::to(auth()->user()->email)
            ->queue(new ReservaPendiente($reserva));

        return response()->json([
            'message' => 'Reserva enviada, en espera de aprobación del administrador',
            'reserva' => new ReservaResource(
                $reserva->load('embarcacion', 'pasajeros', 'boletos')
            ),
        ], 201);
    });
}

    /**
     * Cancelar reserva
     *
     * Cancela una reserva del usuario autenticado. Solo se puede cancelar si la fecha es posterior a hoy.
     * Invalida automáticamente el boleto asociado.
     *
     * @authenticated
     *
     * @urlParam id integer required ID de la reserva. Example: 1
     *
     * @response 200 {"message": "Reserva cancelada correctamente"}
     * @response 400 scenario="Ya cancelada" {"error": "La reserva ya está cancelada"}
     * @response 400 scenario="Fecha pasada" {"error": "No se puede cancelar una reserva del día actual o pasada"}
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function cancelar($id)
    {
        $reserva = Reserva::where('user_id', auth()->id())->findOrFail($id);

        if ($reserva->estado === 'cancelada') {
            return response()->json(['error' => 'La reserva ya está cancelada'], 400);
        }

        if ($reserva->fecha <= now()->toDateString()) {
            return response()->json([
                'error' => 'No se puede cancelar una reserva del día actual o pasada',
            ], 400);
        }

        DB::transaction(function () use ($reserva) {
            $reserva->update(['estado' => 'cancelada']);
            $reserva->boleto?->update(['estado' => 'cancelado']);
        });

        return response()->json(['message' => 'Reserva cancelada correctamente']);
    }

    /**
     * Reservas por fecha
     *
     * Retorna las reservas del usuario autenticado filtradas por fecha.
     *
     * @authenticated
     *
     * @queryParam fecha string required Fecha a consultar (YYYY-MM-DD). Example: 2026-05-10
     *
     * @response 200 {
     *   "data": [
     *     {"id": 1, "fecha": "2026-05-10", "estado": "confirmada"}
     *   ]
     * }
     */
    public function porFecha(Request $request)
    {
        $request->validate(['fecha' => 'required|date']);

        $reservas = Reserva::with('pasajeros', 'embarcacion', 'boleto')
            ->where('user_id', auth()->id())
            ->whereDate('fecha', $request->query('fecha'))
            ->get();

        return ReservaResource::collection($reservas);
    }

    /**
     * Reservas de hoy (Operador)
     *
     * Retorna todas las reservas activas del día actual para validación en puerto.
     * Requiere rol operador o admin.
     *
     * @authenticated
     *
     * @queryParam embarcacion_id integer Filtrar por embarcación específica. Example: 1
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "fecha": "2026-04-13",
     *       "total_personas": 2,
     *       "estado": "confirmada",
     *       "boleto": {"codigo_qr": "01KP2EZ650JKEFGPEZHGQWM5PD"}
     *     }
     *   ]
     * }
     */
    public function hoy(Request $request)
    {
        $query = Reserva::with('pasajeros', 'embarcacion', 'boleto', 'user', 'viaje', 'decididoPor', 'canceladoPor')
            ->whereDate('fecha', today());

        if ($request->has('embarcacion_id')) {
            $query->where('embarcacion_id', $request->embarcacion_id);
        }

        return ReservaResource::collection(
            $query->whereNotIn('estado', ['cancelada', 'rechazada', 'pendiente'])->get()
        );
    }

    /**
     * Listar todas las reservas (Admin)
     *
     * Retorna todas las reservas del sistema con filtros opcionales. Paginado.
     * Requiere rol admin.
     *
     * @authenticated
     *
     * @queryParam fecha string Filtrar por fecha (YYYY-MM-DD). Example: 2026-05-10
     * @queryParam embarcacion_id integer Filtrar por embarcación. Example: 1
     * @queryParam estado string Filtrar por estado: confirmada, cancelada, completada. Example: confirmada
     * @queryParam per_page integer Resultados por página (default: 20). Example: 10
     *
     * @response 200 {
     *   "data": [],
     *   "current_page": 1,
     *   "total": 0,
     *   "per_page": 20
     * }
     */
    public function adminIndex(Request $request)
    {
        $query = Reserva::with('pasajeros', 'embarcacion', 'boleto', 'user', 'viaje', 'decididoPor', 'canceladoPor');

        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }

        if ($request->filled('embarcacion_id')) {
            $query->where('embarcacion_id', $request->embarcacion_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        return ReservaResource::collection(
            $query->latest()->paginate($request->get('per_page', 20))
        );
    }

    /**
     * Cancelar / desautorizar reserva (Admin)
     *
     * Cancela una reserva (típicamente ya confirmada), invalida su boleto y libera
     * el horario para nuevas reservas. Notifica al usuario por correo indicando el
     * motivo de la cancelación y pidiéndole reservar para otra fecha. Requiere rol admin.
     *
     * @authenticated
     *
     * @urlParam id integer required ID de la reserva. Example: 1
     * @bodyParam motivo string required Motivo de la cancelación/desautorización. Example: Embarcación en mantenimiento
     *
     * @response 200 {"message": "Reserva cancelada correctamente"}
     * @response 400 scenario="Ya cancelada" {"error": "La reserva ya está cancelada"}
     * @response 422 scenario="Sin motivo" {"error": "El motivo es requerido"}
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function adminCancelar(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);

        if ($reserva->estado === 'cancelada') {
            return response()->json(['error' => 'La reserva ya está cancelada'], 400);
        }

        $request->validate([
            'motivo' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($reserva, $request) {
            $reserva->update([
                'estado'             => 'cancelada',
                'motivo_cancelacion' => $request->motivo,
                'cancelado_por_id'   => auth()->id(),
            ]);
            $reserva->boleto?->update(['estado' => 'cancelado']);
        });

        Mail::to($reserva->user->email)
            ->queue(new ReservaCancelada($reserva->fresh(['embarcacion', 'pasajeros', 'user'])));

        return response()->json(['message' => 'Reserva cancelada correctamente']);
    }

    /**
     * Aprobar reserva (Admin)
     *
     * Aprueba una reserva pendiente: la confirma, genera el boleto QR y envía el correo de confirmación.
     * La fecha y horario de la embarcación quedan bloqueados para nuevas reservas a partir de este momento.
     * Si ya existe otra reserva confirmada en conflicto de horario, la aprobación se rechaza.
     * Requiere rol admin.
     *
     * @authenticated
     *
     * @urlParam id integer required ID de la reserva. Example: 1
     *
     * @response 200 {"message": "Reserva aprobada correctamente"}
     * @response 400 scenario="No pendiente" {"error": "Solo se pueden aprobar reservas pendientes"}
     * @response 409 scenario="Horario ya confirmado" {"error": "Ya existe otra reserva confirmada para esa fecha y horario"}
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function adminAprobar($id)
    {
        $reserva = Reserva::findOrFail($id);

        if ($reserva->estaVencidaPendiente()) {
            $reserva->marcarVencida();
        }

        if ($reserva->estado !== 'pendiente') {
            return response()->json(['error' => 'Solo se pueden aprobar reservas pendientes'], 400);
        }

        return DB::transaction(function () use ($reserva) {
            // Bloqueo: si otra reserva ya fue confirmada para el mismo horario,
            // esta ya no puede aprobarse (la fecha/hora se libera solo hasta que
            // un administrador autoriza la primera).
            $conflicto = Reserva::where('embarcacion_id', $reserva->embarcacion_id)
                ->where('id', '!=', $reserva->id)
                ->whereDate('fecha', $reserva->fecha)
                ->where('estado', 'confirmada')
                ->where(function ($q) use ($reserva) {
                    $q->where('hora_inicio', '<', $reserva->hora_fin)
                      ->where('hora_fin', '>', $reserva->hora_inicio);
                })
                ->lockForUpdate()
                ->exists();

            if ($conflicto) {
                return response()->json([
                    'error' => 'Ya existe otra reserva confirmada para esa fecha y horario',
                ], 409);
            }

            $reserva->update([
                'estado'          => 'confirmada',
                'decidido_por_id' => auth()->id(),
            ]);

            $reserva->boletos()->create([
                'codigo_qr' => Boleto::generarCodigo(),
                'estado'    => 'valido',
            ]);

            Mail::to($reserva->user->email)
                ->queue(new ReservaConfirmada($reserva->fresh(['boleto', 'embarcacion', 'pasajeros', 'user'])));

            return response()->json(['message' => 'Reserva aprobada correctamente']);
        });
    }

    /**
     * Rechazar reserva (Admin)
     *
     * Rechaza una reserva pendiente y notifica al usuario por correo con el motivo del rechazo,
     * indicándole que puede reservar para otra fecha. Libera el horario/cupo ocupado.
     * Requiere rol admin.
     *
     * @authenticated
     *
     * @urlParam id integer required ID de la reserva. Example: 1
     * @bodyParam motivo string required Motivo del rechazo. Example: No hay disponibilidad de tripulación
     *
     * @response 200 {"message": "Reserva rechazada correctamente"}
     * @response 400 scenario="No pendiente" {"error": "Solo se pueden rechazar reservas pendientes"}
     * @response 422 scenario="Sin motivo" {"error": "El motivo es requerido"}
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function adminRechazar(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);

        if ($reserva->estaVencidaPendiente()) {
            $reserva->marcarVencida();
        }

        if ($reserva->estado !== 'pendiente') {
            return response()->json(['error' => 'Solo se pueden rechazar reservas pendientes'], 400);
        }

        $request->validate([
            'motivo' => 'required|string|max:500',
        ]);

        $reserva->update([
            'estado'             => 'rechazada',
            'motivo_cancelacion' => $request->motivo,
            'decidido_por_id'    => auth()->id(),
        ]);

        Mail::to($reserva->user->email)
            ->queue(new ReservaRechazada($reserva));

        return response()->json(['message' => 'Reserva rechazada correctamente']);
    }

    public function adminDestroy($id)
    {
        $reserva = Reserva::findOrFail($id);

        $reserva->boleto?->delete();
        $reserva->delete();

        return response()->json(['message' => 'Reserva eliminada correctamente']);
    }

    /**
     * Restaurar reserva (Admin)
     *
     * Restaura una reserva eliminada junto con su boleto. Requiere rol admin.
     *
     * @authenticated
     *
     * @urlParam id integer required ID de la reserva eliminada. Example: 1
     *
     * @response 200 {"message": "Reserva restaurada correctamente"}
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function adminRestore($id)
    {
        $reserva = Reserva::onlyTrashed()->findOrFail($id);
        $reserva->restore();

        $boleto = Boleto::onlyTrashed()->where('reserva_id', $id)->first();
        $boleto?->restore();

        return response()->json(['message' => 'Reserva restaurada correctamente']);
    }
}