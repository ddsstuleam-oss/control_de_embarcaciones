<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Pasajero;
use App\Models\Actividad;
use App\Http\Resources\PasajeroResource;
use App\Http\Resources\ReservaResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Pasajeros (embarque)
 *
 * Edición de pasajeros de una reserva ya confirmada, en el momento del embarque.
 * Permite al operador agregar, editar o quitar pasajeros de último momento antes
 * de confirmar el embarque final. Requiere rol operador o admin.
 */
class PasajeroController extends Controller
{
    /**
     * Busca la reserva y valida que todavía se pueda editar su lista de pasajeros:
     * su viaje operativo debe existir y estar en estado ABORDANDO.
     */
    private function reservaEditable($reservaId): Reserva
    {
        $reserva = Reserva::with('embarcacion', 'boleto', 'pasajeros', 'viaje')->findOrFail($reservaId);

        if (!$reserva->viaje || $reserva->viaje->estado !== 'abordando') {
            abort(400, 'Solo se pueden editar pasajeros mientras el viaje está en estado de abordaje');
        }

        return $reserva;
    }

    /**
     * Agregar pasajero (último momento)
     *
     * Agrega un pasajero adicional a una reserva confirmada, siempre que la
     * embarcación aún tenga cupo disponible. Actualiza el total de pasajeros.
     *
     * @authenticated
     *
     * @urlParam reserva integer required ID de la reserva. Example: 1
     * @bodyParam nombre string required Nombre completo. Example: Ana Torres
     * @bodyParam cedula string required Cédula (10 dígitos). Example: 0950638675
     * @bodyParam tipo string required estudiante, docente, administrativo o externo. Example: externo
     * @bodyParam carrera string Carrera (opcional). Example: Medicina
     * @bodyParam facultad string Facultad a la que pertenece la carrera (opcional). Example: Facultad Ciencias de la Salud
     * @bodyParam telefono string Teléfono (opcional). Example: 0991234567
     * @bodyParam email string Correo (opcional). Example: ana@example.com
     *
     * @response 201 {"message": "Pasajero agregado correctamente"}
     * @response 400 scenario="Sin cupo" {"error": "No hay cupo disponible en la embarcación"}
     * @response 400 scenario="No editable" {"error": "Solo se pueden editar pasajeros de una reserva confirmada"}
     */
    public function store(Request $request, $reserva)
    {
        $request->validate([
            'nombre'   => 'required|string',
            'cedula'   => 'required|digits:10',
            'tipo'     => 'required|in:estudiante,docente,administrativo,externo',
            'carrera'  => 'nullable|string',
            'facultad' => 'nullable|string',
            'telefono' => 'nullable|string',
            'email'    => 'nullable|email',
        ]);

        $reservaModel = $this->reservaEditable($reserva);

        return DB::transaction(function () use ($reservaModel, $request) {
            $actuales = $reservaModel->pasajeros()->count();

            if ($actuales + 1 > $reservaModel->embarcacion->capacidad) {
                abort(400, 'No hay cupo disponible en la embarcación');
            }

            $pasajero = $reservaModel->pasajeros()->create([
                'nombre'   => $request->nombre,
                'cedula'   => $request->cedula,
                'tipo'     => $request->tipo,
                'carrera'  => $request->carrera,
                'facultad' => $request->facultad,
                'telefono' => $request->telefono,
                'email'    => $request->email,
            ])->fresh();

            $reservaModel->update(['total_personas' => $actuales + 1]);

            Actividad::create([
                'user_id'     => auth()->id(),
                'accion'      => 'pasajero_agregado_embarque',
                'descripcion' => 'Pasajero ' . $pasajero->nombre . ' agregado en el embarque de la reserva #' .
                                 str_pad($reservaModel->id, 6, '0', STR_PAD_LEFT),
                'ip'          => request()->ip(),
                'dispositivo' => request()->header('User-Agent'),
            ]);

            return response()->json([
                'message'  => 'Pasajero agregado correctamente',
                'pasajero' => new PasajeroResource($pasajero),
                'reserva'  => new ReservaResource($reservaModel->fresh(['pasajeros', 'embarcacion', 'boleto'])),
            ], 201);
        });
    }

    /**
     * Editar pasajero (último momento)
     *
     * Corrige los datos de un pasajero de una reserva confirmada antes del embarque final.
     *
     * @authenticated
     *
     * @urlParam reserva integer required ID de la reserva. Example: 1
     * @urlParam pasajero integer required ID del pasajero. Example: 1
     * @bodyParam nombre string Nombre completo. Example: Ana Torres
     * @bodyParam cedula string Cédula (10 dígitos). Example: 0950638675
     * @bodyParam tipo string estudiante, docente, administrativo o externo. Example: externo
     * @bodyParam carrera string Carrera (opcional). Example: Medicina
     * @bodyParam facultad string Facultad a la que pertenece la carrera (opcional). Example: Facultad Ciencias de la Salud
     * @bodyParam telefono string Teléfono (opcional). Example: 0991234567
     * @bodyParam email string Correo (opcional). Example: ana@example.com
     *
     * @response 200 {"message": "Pasajero actualizado correctamente"}
     */
    public function update(Request $request, $reserva, $pasajero)
    {
        $request->validate([
            'nombre'   => 'sometimes|required|string',
            'cedula'   => 'sometimes|required|digits:10',
            'tipo'     => 'sometimes|required|in:estudiante,docente,administrativo,externo',
            'carrera'  => 'nullable|string',
            'facultad' => 'nullable|string',
            'telefono' => 'nullable|string',
            'email'    => 'nullable|email',
        ]);

        $reservaModel = $this->reservaEditable($reserva);
        $pasajeroModel = $reservaModel->pasajeros()->findOrFail($pasajero);

        $pasajeroModel->update($request->only([
            'nombre', 'cedula', 'tipo', 'carrera', 'facultad', 'telefono', 'email',
        ]));

        Actividad::create([
            'user_id'     => auth()->id(),
            'accion'      => 'pasajero_editado_embarque',
            'descripcion' => 'Pasajero ' . $pasajeroModel->nombre . ' editado en el embarque de la reserva #' .
                             str_pad($reservaModel->id, 6, '0', STR_PAD_LEFT),
            'ip'          => request()->ip(),
            'dispositivo' => request()->header('User-Agent'),
        ]);

        return response()->json([
            'message'  => 'Pasajero actualizado correctamente',
            'pasajero' => new PasajeroResource($pasajeroModel),
        ]);
    }

    /**
     * Quitar pasajero (no asistió)
     *
     * Elimina de la reserva a un pasajero que no se presentó al embarque.
     * Actualiza el total de pasajeros y libera un cupo.
     *
     * @authenticated
     *
     * @urlParam reserva integer required ID de la reserva. Example: 1
     * @urlParam pasajero integer required ID del pasajero. Example: 1
     *
     * @response 200 {"message": "Pasajero eliminado correctamente"}
     * @response 400 scenario="Único pasajero" {"error": "La reserva debe tener al menos un pasajero"}
     */
    public function destroy($reserva, $pasajero)
    {
        $reservaModel = $this->reservaEditable($reserva);
        $pasajeroModel = $reservaModel->pasajeros()->findOrFail($pasajero);

        return DB::transaction(function () use ($reservaModel, $pasajeroModel) {
            $restantes = $reservaModel->pasajeros()->count() - 1;

            if ($restantes < 1) {
                abort(400, 'La reserva debe tener al menos un pasajero');
            }

            $nombre = $pasajeroModel->nombre;
            $pasajeroModel->delete();

            $reservaModel->update(['total_personas' => $restantes]);

            Actividad::create([
                'user_id'     => auth()->id(),
                'accion'      => 'pasajero_eliminado_embarque',
                'descripcion' => 'Pasajero ' . $nombre . ' eliminado (no asistió) del embarque de la reserva #' .
                                 str_pad($reservaModel->id, 6, '0', STR_PAD_LEFT),
                'ip'          => request()->ip(),
                'dispositivo' => request()->header('User-Agent'),
            ]);

            return response()->json(['message' => 'Pasajero eliminado correctamente']);
        });
    }
}
