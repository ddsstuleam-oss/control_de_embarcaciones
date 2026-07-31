<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actividad;
use App\Http\Resources\ActividadResource;

/**
 * @group Actividad
 *
 * Endpoints para registro de actividad del usuario autenticado.
 * Flutter debe llamar a este endpoint para registrar acciones importantes
 * como login, creación de reservas, descarga de boletos, etc.
 */
class ActividadController extends Controller
{
    /**
     * Registrar actividad
     *
     * Registra una acción del usuario autenticado para auditoría institucional.
     * La IP se captura automáticamente del servidor.
     * Recomendado llamar desde Flutter en: login, reserva creada, boleto descargado, logout.
     *
     * @authenticated
     *
     * @bodyParam accion string required Nombre de la acción realizada (max 100 caracteres). Example: login
     * @bodyParam descripcion string Descripción detallada de la acción (max 500 caracteres). Example: Inicio de sesión exitoso desde app móvil
     * @bodyParam dispositivo string Nombre o descripción del dispositivo (max 255 caracteres). Example: Samsung Galaxy S23 Android 14
     *
     * @response 201 scenario="Registrado" {
     *   "message": "Actividad registrada",
     *   "actividad": {
     *     "id": 1,
     *     "accion": "login",
     *     "descripcion": "Inicio de sesión exitoso desde app móvil",
     *     "ip": "127.0.0.1",
     *     "dispositivo": "Samsung Galaxy S23 Android 14",
     *     "fecha": "13/04/2026 22:10"
     *   }
     * }
     * @response 422 scenario="Validación" {
     *   "message": "Error de validación",
     *   "errors": {"accion": ["The accion field is required."]}
     * }
     *
     * @responseField id integer ID de la actividad registrada.
     * @responseField accion string Acción registrada.
     * @responseField descripcion string Descripción de la acción.
     * @responseField ip string IP desde donde se realizó la acción.
     * @responseField dispositivo string Dispositivo desde donde se realizó la acción.
     * @responseField fecha string Fecha y hora formateada (d/m/Y H:i).
     */
    public function store(Request $request)
    {
        $request->validate([
            'accion'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'dispositivo' => 'nullable|string|max:255',
        ]);

        $actividad = Actividad::create([
            'user_id'     => auth()->id(),
            'accion'      => $request->accion,
            'descripcion' => $request->descripcion,
            'ip'          => $request->ip(),
            'dispositivo' => $request->dispositivo,
        ]);

        return response()->json([
            'message'   => 'Actividad registrada',
            'actividad' => new ActividadResource($actividad),
        ], 201);
    }
}