<?php

namespace App\Http\Controllers;

use App\Models\Embarcacion;
use App\Models\Reserva;
use App\Http\Resources\EmbarcacionResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * @group Embarcaciones
 *
 * Endpoints para consulta y gestión de embarcaciones.
 * Los endpoints de listado y disponibilidad son públicos.
 * Los endpoints de creación, actualización y eliminación requieren rol admin.
 */
class EmbarcacionController extends Controller
{
    /**
     * Listar embarcaciones
     *
     * Retorna todas las embarcaciones registradas ordenadas por nombre.
     * Endpoint público, no requiere autenticación.
     *
     * @response 200 scenario="Éxito" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "nombre": "Lancha Uleam I",
     *       "capacidad": 25,
     *       "estado": "disponible",
     *       "descripcion": "Embarcación principal para recorridos académicos",
     *       "imagen_url": "http://localhost:8000/storage/embarcaciones/lancha.jpg",
     *       "creado_en": "09/04/2026"
     *     }
     *   ]
     * }
     */
    public function index()
    {
        return EmbarcacionResource::collection(Embarcacion::orderBy('nombre')->get());
    }

    /**
     * Ver embarcación
     *
     * Retorna el detalle de una embarcación específica.
     *
     * @authenticated
     *
     * @urlParam id integer required ID de la embarcación. Example: 1
     *
     * @response 200 scenario="Éxito" {
     *   "data": {
     *     "id": 1,
     *     "nombre": "Lancha Uleam I",
     *     "capacidad": 25,
     *     "estado": "disponible",
     *     "descripcion": "Embarcación principal para recorridos académicos",
     *     "imagen_url": null,
     *     "creado_en": "09/04/2026"
     *   }
     * }
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function show($id)
    {
        return new EmbarcacionResource(Embarcacion::findOrFail($id));
    }

    /**
     * Disponibilidad por fecha
     *
     * Retorna la disponibilidad de cupos de todas las embarcaciones activas para una fecha específica.
     * Endpoint público, no requiere autenticación.
     *
     * @queryParam fecha string required Fecha a consultar (YYYY-MM-DD), debe ser hoy o futura. Example: 2026-05-10
     *
     * @response 200 scenario="Éxito" {
     *   "fecha": "2026-05-10",
     *   "embarcaciones": [
     *     {
     *       "id": 1,
     *       "nombre": "Lancha Uleam I",
     *       "capacidad": 25,
     *       "imagen_url": null,
     *       "reservados": 10,
     *       "disponibles": 15,
     *       "disponible": true
     *     }
     *   ]
     * }
     * @response 422 scenario="Fecha inválida" {
     *   "message": "Error de validación",
     *   "errors": {"fecha": ["The fecha field must be a date after or equal to today."]}
     * }
     */
public function disponibilidad(Request $request)
{
    $request->validate([
        'fecha' => 'required|date|after_or_equal:today',
    ]);

    $embarcaciones = Embarcacion::where('estado', 'disponible')
        ->get()
        ->map(function ($embarcacion) use ($request) {

            // Cantidad de reservas activas en la fecha (informativo).
            // Los cupos se controlan por horario al momento de reservar
            // (ver ReservaController::store), así que aquí no se descuenta
            // del total: cada reserva dispone del cupo completo en su horario.
            $totalReservas = Reserva::where('embarcacion_id', $embarcacion->id)
                ->whereDate('fecha', $request->fecha)
                ->whereNotIn('estado', ['cancelada', 'rechazada'])
                ->count();

            return [
                'id'           => $embarcacion->id,
                'nombre'       => $embarcacion->nombre,
                'capacidad'    => $embarcacion->capacidad,
                'descripcion'  => $embarcacion->descripcion,
                'imagen_url'   => $embarcacion->imagen_url,
                'reservados'   => 0,
                'disponibles'  => $embarcacion->capacidad,
                'porcentaje'   => 0,
                'disponible'   => true,
                'reservas_hoy' => $totalReservas,
            ];
        });

    return response()->json([
        'fecha'         => $request->fecha,
        'embarcaciones' => $embarcaciones,
    ]);
}

    /**
     * Crear embarcación
     *
     * Crea una nueva embarcación en el sistema. Requiere rol admin.
     * Soporta subida de imagen en formato multipart/form-data.
     *
     * @authenticated
     *
     * @bodyParam nombre string required Nombre único de la embarcación. Example: Lancha Uleam III
     * @bodyParam capacidad integer required Capacidad máxima de pasajeros (1-500). Example: 30
     * @bodyParam estado string Estado inicial: disponible o mantenimiento (default: disponible). Example: disponible
     * @bodyParam descripcion string Descripción de la embarcación. Example: Embarcación de transporte estudiantil
     * @bodyParam imagen file Imagen de la embarcación (jpg, png, webp, max 2MB).
     *
     * @response 201 scenario="Creada" {
     *   "message": "Embarcación creada correctamente",
     *   "embarcacion": {
     *     "id": 5,
     *     "nombre": "Lancha Uleam III",
     *     "capacidad": 30,
     *     "estado": "disponible",
     *     "descripcion": "Embarcación de transporte estudiantil",
     *     "imagen_url": null,
     *     "creado_en": "13/04/2026"
     *   }
     * }
     * @response 422 scenario="Nombre duplicado" {
     *   "message": "Error de validación",
     *   "errors": {"nombre": ["The nombre has already been taken."]}
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255|unique:embarcaciones,nombre',
            'capacidad'   => 'required|integer|min:1|max:500',
            'estado'      => 'sometimes|in:disponible,mantenimiento',
            'descripcion' => 'nullable|string|max:1000',
            'imagen'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagenPath = null;
        if ($request->hasFile('imagen')) {
            $imagenPath = $request->file('imagen')->store('embarcaciones', 'public');
        }

        $embarcacion = Embarcacion::create([
            'nombre'      => $request->nombre,
            'capacidad'   => $request->capacidad,
            'estado'      => $request->estado ?? 'disponible',
            'descripcion' => $request->descripcion,
            'imagen'      => $imagenPath,
        ]);

        return response()->json([
            'message'     => 'Embarcación creada correctamente',
            'embarcacion' => new EmbarcacionResource($embarcacion),
        ], 201);
    }

    /**
     * Actualizar embarcación
     *
     * Actualiza los datos de una embarcación. Requiere rol admin.
     * No permite poner en mantenimiento si hay reservas futuras activas.
     *
     * @authenticated
     *
     * @urlParam id integer required ID de la embarcación. Example: 1
     * @bodyParam nombre string Nuevo nombre único. Example: Lancha Uleam I Renovada
     * @bodyParam capacidad integer Nueva capacidad (1-500). Example: 30
     * @bodyParam estado string Nuevo estado: disponible o mantenimiento. Example: mantenimiento
     * @bodyParam descripcion string Nueva descripción. Example: Embarcación renovada 2026
     * @bodyParam imagen file Nueva imagen (jpg, png, webp, max 2MB).
     *
     * @response 200 scenario="Actualizada" {
     *   "message": "Embarcación actualizada correctamente",
     *   "embarcacion": {
     *     "id": 1,
     *     "nombre": "Lancha Uleam I Renovada",
     *     "capacidad": 30,
     *     "estado": "disponible"
     *   }
     * }
     * @response 400 scenario="Reservas futuras" {
     *   "error": "No se puede poner en mantenimiento, hay reservas futuras activas",
     *   "reservas_futuras": 3
     * }
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function update(Request $request, $id)
    {
        $embarcacion = Embarcacion::findOrFail($id);

        $request->validate([
            'nombre'      => 'sometimes|string|max:255|unique:embarcaciones,nombre,' . $id,
            'capacidad'   => 'sometimes|integer|min:1|max:500',
            'estado'      => 'sometimes|in:disponible,mantenimiento',
            'descripcion' => 'nullable|string|max:1000',
            'imagen'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->estado === 'mantenimiento') {
            $reservasFuturas = Reserva::where('embarcacion_id', $id)
                ->where('fecha', '>=', now()->toDateString())
                ->whereNotIn('estado', ['cancelada', 'rechazada'])
                ->count();

            if ($reservasFuturas > 0) {
                return response()->json([
                    'error'            => 'No se puede poner en mantenimiento, hay reservas futuras activas',
                    'reservas_futuras' => $reservasFuturas,
                ], 400);
            }
        }

        $data = $request->only(['nombre', 'capacidad', 'estado', 'descripcion']);

        if ($request->hasFile('imagen')) {
            if ($embarcacion->imagen) {
                Storage::disk('public')->delete($embarcacion->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('embarcaciones', 'public');
        }

        $embarcacion->update($data);

        return response()->json([
            'message'     => 'Embarcación actualizada correctamente',
            'embarcacion' => new EmbarcacionResource($embarcacion),
        ]);
    }

    /**
     * Eliminar embarcación
     *
     * Elimina una embarcación del sistema. Requiere rol admin.
     * No permite eliminar si tiene reservas futuras activas.
     * Elimina también la imagen del storage si existe.
     *
     * @authenticated
     *
     * @urlParam id integer required ID de la embarcación. Example: 1
     *
     * @response 200 {"message": "Embarcación eliminada correctamente"}
     * @response 400 scenario="Reservas futuras" {
     *   "error": "No se puede eliminar, tiene reservas futuras activas",
     *   "reservas_futuras": 2
     * }
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function destroy($id)
    {
        $embarcacion = Embarcacion::findOrFail($id);

        $reservasFuturas = Reserva::where('embarcacion_id', $id)
            ->where('fecha', '>=', now()->toDateString())
            ->whereNotIn('estado', ['cancelada', 'rechazada'])
            ->count();

        if ($reservasFuturas > 0) {
            return response()->json([
                'error'            => 'No se puede eliminar, tiene reservas futuras activas',
                'reservas_futuras' => $reservasFuturas,
            ], 400);
        }

        if ($embarcacion->imagen) {
            Storage::disk('public')->delete($embarcacion->imagen);
        }

        $embarcacion->delete();

        return response()->json([
            'message' => 'Embarcación eliminada correctamente',
        ]);
    }

    /**
     * Horarios ocupados
     *
     * Retorna los horarios ocupados de una embarcación para una fecha específica.
     * Solo se consideran ocupados los horarios de reservas ya confirmadas por un
     * administrador; las reservas pendientes no bloquean el horario.
     * Endpoint público, no requiere autenticación.
     *
     * @urlParam id integer required ID de la embarcación. Example: 1
     * @queryParam fecha string required Fecha a consultar (YYYY-MM-DD). Example: 2026-05-10
     *
     * @response 200 scenario="Éxito" {
     *   "horarios_ocupados": [
     *     {
     *       "hora_inicio": "09:00:00",
     *       "hora_fin": "11:00:00"
     *     },
     *     {
     *       "hora_inicio": "14:00:00",
     *       "hora_fin": "16:00:00"
     *     }
     *   ]
     * }
     * @response 422 scenario="Fecha inválida" {
     *   "message": "Error de validación",
     *   "errors": {"fecha": ["El campo de fecha debe contener una fecha válida."]}
     * }
     */


    public function horariosOcupados(Request $request, $id)
{
    $request->validate(['fecha' => 'required|date']);

    $horarios = Reserva::where('embarcacion_id', $id)
        ->whereDate('fecha', $request->fecha)
        ->where('estado', 'confirmada')
        ->get(['hora_inicio', 'hora_fin'])
        ->map(fn($r) => [
            'hora_inicio' => $r->hora_inicio,
            'hora_fin'    => $r->hora_fin,
        ]);

    return response()->json(['horarios_ocupados' => $horarios]);
}
}