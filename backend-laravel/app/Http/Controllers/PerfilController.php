<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Http\Resources\UserResource;
use App\Http\Resources\ReservaResource;
use App\Http\Resources\ActividadResource;
use App\Notifications\VerifyEmailNotification;

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
     * Actualiza el nombre y/o telefono del usuario autenticado. El email no
     * se cambia aqui - ver solicitarCambioEmail()/confirmarCambioEmail(),
     * que exigen verificar el correo nuevo antes de aplicarlo.
     *
     * @authenticated
     *
     * @bodyParam name string Nombre completo del usuario. Example: Juan Carlos Pérez
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
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name'     => 'sometimes|string|max:255',
            'telefono' => 'sometimes|digits_between:7,10',
        ], [
            'telefono.digits_between' => 'El teléfono debe tener entre 7 y 10 dígitos.',
        ]);

        $user->update($request->only(['name', 'telefono']));

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'usuario' => new UserResource($user),
        ]);
    }

    /**
     * Solicitar cambio de email
     *
     * Envia un codigo de verificacion de 6 digitos al correo nuevo. El
     * correo actual sigue activo hasta que se confirme el codigo con
     * confirmarCambioEmail().
     *
     * @authenticated
     *
     * @bodyParam email string required Correo electronico nuevo. Example: nuevo@uleam.edu.ec
     *
     * @response 200 scenario="Éxito" {
     *   "message": "Código de verificación enviado a tu nuevo correo."
     * }
     */
    public function solicitarCambioEmail(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'email' => 'required|email|unique:users,email',
        ], [
            'email.unique' => 'Ese correo ya está en uso por otra cuenta.',
        ]);

        if ($request->email === $user->email) {
            return response()->json([
                'error' => 'Ese ya es tu correo actual.',
            ], 422);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'email_pendiente'         => $request->email,
            'email_verification_code' => $code,
            'email_code_expires_at'   => now()->addMinutes(30),
        ]);

        Notification::route('mail', $request->email)
            ->notify(new VerifyEmailNotification($code));

        return response()->json([
            'message' => 'Código de verificación enviado a tu nuevo correo.',
        ]);
    }

    /**
     * Confirmar cambio de email
     *
     * Verifica el codigo enviado al correo nuevo y, si es valido, lo
     * aplica como el email principal de la cuenta.
     *
     * @authenticated
     *
     * @bodyParam code string required Código de 6 dígitos recibido en el correo nuevo. Example: 482913
     *
     * @response 200 scenario="Éxito" {
     *   "message": "Correo actualizado correctamente",
     *   "usuario": {"id": 3, "email": "nuevo@uleam.edu.ec"}
     * }
     */
    public function confirmarCambioEmail(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        if (!$user->email_pendiente) {
            return response()->json([
                'error' => 'No hay ningún cambio de correo pendiente.',
            ], 400);
        }

        if (now()->isAfter($user->email_code_expires_at)) {
            return response()->json([
                'error' => 'El código ha expirado. Solicita uno nuevo.',
            ], 400);
        }

        if ($user->email_verification_code !== $request->code) {
            return response()->json([
                'error' => 'Código incorrecto.',
            ], 400);
        }

        $user->forceFill([
            'email'                    => $user->email_pendiente,
            'email_pendiente'          => null,
            'email_verification_code'  => null,
            'email_code_expires_at'    => null,
            'email_verified_at'        => now(),
        ])->save();

        return response()->json([
            'message' => 'Correo actualizado correctamente',
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