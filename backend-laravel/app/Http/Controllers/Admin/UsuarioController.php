<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Rules\CedulaEcuatoriana;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

/**
 * @group Admin — Usuarios
 *
 * Endpoints para gestión completa de usuarios del sistema.
 * Todos los endpoints requieren rol admin.
 */
class UsuarioController extends Controller
{
    /**
     * Listar usuarios
     *
     * Retorna todos los usuarios del sistema con filtros opcionales. Paginado.
     *
     * @authenticated
     *
     * @queryParam activo boolean Filtrar por estado: true=activos, false=inactivos. Example: true
     * @queryParam rol string Filtrar por rol: admin, operador, usuario. Example: usuario
     * @queryParam buscar string Buscar por nombre, cédula o email. Example: Juan
     * @queryParam per_page integer Resultados por página (default: 20). Example: 10
     *
     * @response 200 scenario="Éxito" {
     *   "data": [
     *     {
     *       "id": 3,
     *       "cedula": "1300000003",
     *       "nombre": "Estudiante Test",
     *       "email": "estudiante@uleam.edu.ec",
     *       "activo": true,
     *       "rol": "usuario",
     *       "dias_para_vencer": 85,
     *       "miembro_desde": "09/04/2026"
     *     }
     *   ],
     *   "current_page": 1,
     *   "total": 3,
     *   "per_page": 20
     * }
     */
    public function index(Request $request)
    {
        $usuarios = User::with('roles')
            ->when($request->filled('activo'), fn($q) =>
                $q->where('activo', $request->boolean('activo'))
            )
            ->when($request->filled('rol'), fn($q) =>
                $q->whereHas('roles', fn($r) =>
                    $r->where('name', $request->rol)
                )
            )
            ->when($request->filled('buscar'), fn($q) =>
                $q->where(fn($q) =>
                    $q->where('name', 'like', '%' . $request->buscar . '%')
                      ->orWhere('cedula', 'like', '%' . $request->buscar . '%')
                      ->orWhere('email', 'like', '%' . $request->buscar . '%')
                )
            )
            ->orderBy('name')
            ->paginate($request->get('per_page', 20));

        return UserResource::collection($usuarios);
    }

    /**
     * Ver usuario
     *
     * Retorna el detalle de un usuario con sus reservas asociadas.
     *
     * @authenticated
     *
     * @urlParam id integer required ID del usuario. Example: 3
     *
     * @response 200 scenario="Éxito" {
     *   "usuario": {
     *     "id": 3,
     *     "cedula": "1300000003",
     *     "nombre": "Estudiante Test",
     *     "email": "estudiante@uleam.edu.ec",
     *     "activo": true,
     *     "rol": "usuario"
     *   },
     *   "total_reservas": 5
     * }
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function show($id)
    {
        $usuario = User::with('roles', 'reservas.embarcacion')
            ->findOrFail($id);

        return response()->json([
            'usuario'        => new UserResource($usuario),
            'total_reservas' => $usuario->reservas->count(),
        ]);
    }

    /**
     * Activar / Desactivar usuario
     *
     * Alterna el estado activo/inactivo de un usuario.
     * Al desactivar, se cierran todas sus sesiones activas.
     * No se puede desactivar la propia cuenta.
     *
     * @authenticated
     *
     * @urlParam id integer required ID del usuario. Example: 3
     *
     * @response 200 scenario="Desactivado" {
     *   "message": "Usuario desactivado correctamente",
     *   "activo": false
     * }
     * @response 200 scenario="Activado" {
     *   "message": "Usuario activado correctamente",
     *   "activo": true
     * }
     * @response 400 {"error": "No puedes desactivar tu propia cuenta"}
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function toggle($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'error' => 'No puedes desactivar tu propia cuenta',
            ], 400);
        }

        $user->update(['activo' => !$user->activo]);

        if (!$user->activo) {
            $user->tokens()->delete();
        }

        return response()->json([
            'message' => $user->activo ? 'Usuario activado correctamente' : 'Usuario desactivado correctamente',
            'activo'  => $user->activo,
        ]);
    }

    /**
     * Cambiar rol
     *
     * Cambia el rol de un usuario del sistema.
     * No se puede cambiar el propio rol.
     *
     * @authenticated
     *
     * @urlParam id integer required ID del usuario. Example: 3
     * @bodyParam rol string required Nuevo rol: admin, operador, usuario. Example: operador
     *
     * @response 200 scenario="Éxito" {
     *   "message": "Rol actualizado correctamente",
     *   "usuario": {
     *     "id": 3,
     *     "nombre": "Estudiante Test",
     *     "rol": "operador"
     *   }
     * }
     * @response 400 {"error": "No puedes cambiar tu propio rol"}
     * @response 404 {"message": "Recurso no encontrado"}
     * @response 422 scenario="Rol inválido" {
     *   "message": "Error de validación",
     *   "errors": {"rol": ["The selected rol is invalid."]}
     * }
     */
    public function cambiarRol(Request $request, $id)
    {
        $request->validate([
            'rol' => 'required|in:admin,operador,usuario',
        ]);

        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'error' => 'No puedes cambiar tu propio rol',
            ], 400);
        }

        $user->syncRoles([$request->rol]);

        return response()->json([
            'message' => 'Rol actualizado correctamente',
            'usuario' => new UserResource($user),
        ]);
    }

    /**
     * Crear usuario
     *
     * Crea un nuevo usuario desde el panel de administración.
     * Permite asignar cualquier rol directamente.
     * La cédula debe ser ecuatoriana válida.
     *
     * @authenticated
     *
     * @bodyParam cedula string required Cédula ecuatoriana válida de 10 dígitos. Example: 1300000099
     * @bodyParam name string required Nombre completo. Example: María García
     * @bodyParam email string required Correo electrónico único. Example: maria@uleam.edu.ec
     * @bodyParam rol string required Rol asignado: admin, operador, usuario. Example: operador
     * @bodyParam password string required Contraseña inicial mínimo 8 caracteres. Example: Temporal123!
     *
     * @response 201 scenario="Creado" {
     *   "message": "Usuario creado correctamente",
     *   "usuario": {
     *     "id": 5,
     *     "cedula": "1300000099",
     *     "nombre": "María García",
     *     "email": "maria@uleam.edu.ec",
     *     "activo": true,
     *     "rol": "operador"
     *   }
     * }
     * @response 422 scenario="Cédula inválida" {
     *   "message": "Error de validación",
     *   "errors": {"cedula": ["La cédula ecuatoriana no es válida."]}
     * }
     * @response 422 scenario="Email duplicado" {
     *   "message": "Error de validación",
     *   "errors": {"email": ["The email has already been taken."]}
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'cedula'   => ['required', 'digits:10', 'unique:users,cedula', new CedulaEcuatoriana],
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'rol'      => 'required|in:admin,operador,usuario',
            'password' => ['required', Password::min(8)->mixedCase()->numbers()],
        ], [
            'password.min'     => 'La contraseña debe tener mínimo 8 caracteres.',
            'password.mixed'   => 'La contraseña debe combinar mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe incluir al menos un número.',
        ]);

        $user = User::create([
            'cedula'              => $request->cedula,
            'name'                => $request->name,
            'email'               => $request->email,
            'password'            => bcrypt($request->password),
            'password_changed_at' => now(),
            'activo'              => true,
            'email_verified_at'   => now(),
        ]);

        $user->assignRole($request->rol);

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'usuario' => new UserResource($user),
        ], 201);
    }

    /**
     * Eliminar usuario
     *
     * Elimina un usuario del sistema.
     * No se puede eliminar la propia cuenta.
     * No se puede eliminar si tiene reservas futuras activas.
     * Cierra todas las sesiones del usuario antes de eliminarlo.
     *
     * @authenticated
     *
     * @urlParam id integer required ID del usuario. Example: 3
     *
     * @response 200 {"message": "Usuario eliminado correctamente"}
     * @response 400 scenario="Propia cuenta" {"error": "No puedes eliminar tu propia cuenta"}
     * @response 400 scenario="Reservas activas" {
     *   "error": "El usuario tiene reservas futuras activas",
     *   "reservas_futuras": 2
     * }
     * @response 404 {"message": "Recurso no encontrado"}
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'error' => 'No puedes eliminar tu propia cuenta',
            ], 400);
        }

        $reservasFuturas = $user->reservas()
            ->where('fecha', '>=', now()->toDateString())
            ->whereNotIn('estado', ['cancelada'])
            ->count();

        if ($reservasFuturas > 0) {
            return response()->json([
                'error'            => 'El usuario tiene reservas futuras activas',
                'reservas_futuras' => $reservasFuturas,
            ], 400);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado correctamente',
        ]);
    }
}