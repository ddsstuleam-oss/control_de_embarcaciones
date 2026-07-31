<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserResource;
use App\Http\Resources\ReservaResource;
use App\Http\Resources\ActividadResource;

/**
 * @group Perfil
 *
 * Endpoints para gestión del perfil del usuario autenticado.
 */
class PerfilController extends Controller
{
    /**
     * Ver mi perfil
     *
     * Retorna los datos del usuario autenticado junto con estadísticas de sus reservas.
     *
     * @authenticated
     *
     * @response 200 scenario="Éxito" {
     *   "usuario": {
     *     "id": 3,
     *     "cedula": "1300000003",
     *     "nombre": "Estudiante Test",
     *     "email": "estudiante@uleam.edu.ec",
     *     "activo": true,
     *     "rol": "usuario",
     *     "dias_para_vencer": 85,
     *     "miembro_desde": "09/04/2026"
     *   },
     *   "estadisticas": {
     *     "total_reservas": 5,
     *     "reservas_activas": 2,
     *     "reservas_canceladas": 1
     *   }
     * }
     */
    public function index(Request $request)
    {
        $user = $request->user()->load('roles');

        return response()->json([
            'usuario'      => new UserResource($user),
            'estadisticas' => [
                'total_reservas'      => $user->reservas()->count(),
                'reservas_activas'    => $user->reservas()
                    ->where('fecha', '>=', now()->toDateString())
                    ->whereNotIn('estado', ['cancelada'])
                    ->count(),
                'reservas_canceladas' => $user->reservas()
                    ->where('estado', 'cancelada')
                    ->count(),
            ],
        ]);
    }

    /**
     * Actualizar perfil
     *
     * Actualiza el nombre y/o email del usuario autenticado.
     *
     * @authenticated
     *
     * @bodyParam name string Nombre completo del usuario. Example: Juan Carlos Pérez
     * @bodyParam email string Correo electrónico único. Example: juan@uleam.edu.ec
     * @bodyParam telefono string Teléfono de contacto (7 a 10 dígitos). Example: 0991234567
     *
     * @response 200 scenario="Éxito" {
     *   "message": "Perfil actualizado correctamente",
     *   "usuario": {
     *     "id": 3,
     *     "cedula": "1300000003",
     *     "nombre": "Juan Carlos Pérez",
     *     "email": "juan@uleam.edu.ec",
     *     "rol": "usuario"
     *   }
     * }
     * @response 422 scenario="Email duplicado" {
     *   "message": "Error de validación",
     *   "errors": {"email": ["The email has already been taken."]}
     * }
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $user->id,
            'telefono' => 'sometimes|digits_between:7,10',
        ], [
            'telefono.digits_between' => 'El teléfono debe tener entre 7 y 10 dígitos.',
        ]);

        $user->update($request->only(['name', 'email', 'telefono']));

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'usuario' => new UserResource($user),
        ]);
    }

    /**
     * Subir foto de perfil
     *
     * Sube o reemplaza la foto de perfil del usuario autenticado.
     *
     * @authenticated
     *
     * @bodyParam foto file required Imagen de perfil (jpeg, png, jpg, webp, máx. 5MB).
     *
     * @response 200 scenario="Éxito" {
     *   "message": "Foto de perfil actualizada correctamente",
     *   "usuario": {
     *     "id": 3,
     *     "nombre": "Estudiante Test",
     *     "foto_url": "http://localhost:8000/storage/perfiles/xxx.jpg"
     *   }
     * }
     */
    public function subirFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user = $request->user();

        if ($user->foto_perfil) {
            Storage::disk('public')->delete($user->foto_perfil);
        }

        $ruta = $request->file('foto')->store('perfiles', 'public');
        $user->update(['foto_perfil' => $ruta]);

        return response()->json([
            'message' => 'Foto de perfil actualizada correctamente',
            'usuario' => new UserResource($user),
        ]);
    }

    /**
     * Historial de reservas
     *
     * Retorna el historial paginado de reservas del usuario autenticado.
     *
     * @authenticated
     *
     * @queryParam per_page integer Resultados por página (default: 10). Example: 5
     *
     * @response 200 scenario="Éxito" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "fecha": "2026-05-10",
     *       "total_personas": 2,
     *       "estado": "confirmada",
     *       "embarcacion": {"nombre": "Lancha Uleam I"},
     *       "boleto": {"codigo_qr": "01KP2EZ650JKEFGPEZHGQWM5PD", "estado": "valido"}
     *     }
     *   ],
     *   "current_page": 1,
     *   "total": 5,
     *   "per_page": 10
     * }
     */
    public function reservas(Request $request)
    {
        $reservas = $request->user()
            ->reservas()
            ->with('embarcacion', 'pasajeros', 'boleto')
            ->latest()
            ->paginate($request->get('per_page', 10));

        return ReservaResource::collection($reservas);
    }

    /**
     * Historial de actividad
     *
     * Retorna el historial paginado de actividades registradas del usuario autenticado.
     *
     * @authenticated
     *
     * @queryParam per_page integer Resultados por página (default: 20). Example: 10
     *
     * @response 200 scenario="Éxito" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "accion": "login",
     *       "descripcion": "Inicio de sesión exitoso",
     *       "ip": "127.0.0.1",
     *       "dispositivo": "Android",
     *       "fecha": "13/04/2026 21:55"
     *     }
     *   ],
     *   "current_page": 1,
     *   "total": 10,
     *   "per_page": 20
     * }
     */
    public function actividades(Request $request)
    {
        $actividades = $request->user()
            ->actividades()
            ->latest()
            ->paginate($request->get('per_page', 20));

        return ActividadResource::collection($actividades);
    }
}