<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Rules\CedulaEcuatoriana;
use App\Http\Resources\UserResource;
use App\Notifications\VerifyEmailNotification;

/**
 * @group Autenticación
 *
 * Endpoints para registro, login, logout y gestión de contraseña.
 * El sistema usa Laravel Sanctum con tokens Bearer.
 */
class AuthController extends Controller
{
    /**
     * Registro
     *
     * Registra un nuevo usuario en el sistema con rol `usuario` por defecto.
     * La cédula debe ser ecuatoriana válida (algoritmo del Registro Civil).
     *
     * @bodyParam cedula string required Cédula ecuatoriana válida de 10 dígitos. Example: 1300000099
     * @bodyParam name string required Nombre completo. Example: Juan Carlos Pérez
     * @bodyParam email string required Correo electrónico único. Example: juan@uleam.edu.ec
     * @bodyParam telefono string required Teléfono de contacto (7 a 10 dígitos). Example: 0991234567
     * @bodyParam password string required Contraseña mínimo 8 caracteres. Example: Password123!
     * @bodyParam password_confirmation string required Confirmación de contraseña. Example: Password123!
     *
     * @response 201 scenario="Registro exitoso" {
     *   "message": "Registro exitoso",
     *   "user": {
     *     "id": 4,
     *     "cedula": "1300000099",
     *     "nombre": "Juan Carlos Pérez",
     *     "email": "juan@uleam.edu.ec",
     *     "activo": true,
     *     "rol": "usuario",
     *     "dias_para_vencer": 90,
     *     "miembro_desde": "13/04/2026"
     *   },
     *   "token": "4|abc123xyz..."
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
    public function register(Request $request)
    {
        $request->validate([
            'cedula'   => ['required', 'digits:10', 'unique:users,cedula', new CedulaEcuatoriana],
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'telefono' => 'required|digits_between:7,10',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'cedula.required'    => 'La cédula es obligatoria.',
            'cedula.digits'      => 'La cédula debe tener exactamente 10 dígitos.',
            'cedula.unique'      => 'Esta cédula ya está registrada. Inicia sesión o verifica tu correo.',
            'name.required'      => 'El nombre es obligatorio.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'El formato del correo electrónico no es válido.',
            'email.unique'       => 'Este correo ya está registrado. Inicia sesión o verifica tu correo.',
            'telefono.required'         => 'El teléfono es obligatorio.',
            'telefono.digits_between'   => 'El teléfono debe tener entre 7 y 10 dígitos.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener mínimo 8 caracteres.',
            'password.mixed'     => 'La contraseña debe combinar mayúsculas y minúsculas.',
            'password.numbers'   => 'La contraseña debe incluir al menos un número.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'cedula'                  => $request->cedula,
            'name'                    => $request->name,
            'email'                   => $request->email,
            'telefono'                => $request->telefono,
            'password'                => bcrypt($request->password),
            'password_changed_at'     => now(),
            'activo'                  => true,
            'email_verification_code' => $code,
            'email_code_expires_at'   => now()->addMinutes(30),
        ]);

        $user->assignRole('usuario');

        $user->notify(new VerifyEmailNotification($code));

        $token = $user->createToken($this->tokenName($request))->plainTextToken;

        return response()->json([
            'message'               => 'Registro exitoso. Verifica tu correo electrónico.',
            'token'                 => $token,
            'user'                  => new UserResource($user),
            'requires_verification' => true,
        ], 201);
    }

    /**
     * Login
     *
     * Inicia sesión con cédula y contraseña. Retorna el token Bearer para usar en endpoints protegidos.
     * Permite varias sesiones activas a la vez (un token nuevo por dispositivo, sin invalidar los demás).
     * Verifica que la cuenta esté activa y que la contraseña no haya expirado (90 días).
     *
     * @bodyParam cedula string required Cédula ecuatoriana. Example: 1300000001
     * @bodyParam password string required Contraseña. Example: Admin1234!
     *
     * @response 200 scenario="Login exitoso" {
     *   "message": "Inicio de sesión exitoso",
     *   "user": {
     *     "id": 1,
     *     "cedula": "1300000001",
     *     "nombre": "Administrador ULEAM",
     *     "email": "admin@uleam.edu.ec",
     *     "activo": true,
     *     "rol": "admin",
     *     "dias_para_vencer": 85
     *   },
     *   "token": "1|GwT1eMcqABR5AeCiUgmNOBSJZlcOvZlzm2Kr1fVYd7c9feaa"
     * }
     * @response 401 {"message": "Cédula o contraseña incorrecta"}
     * @response 403 scenario="Cuenta desactivada" {"message": "Tu cuenta está desactivada. Contacta al administrador."}
     * @response 403 scenario="Contraseña expirada" {
     *   "message": "Tu contraseña ha expirado. Debes cambiarla.",
     *   "require_password_change": true,
     *   "dias_sin_cambiar": 95
     * }
     */
    public function login(Request $request)
    {
        $request->validate([
            'cedula'   => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('cedula', $request->cedula)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Cédula o contraseña incorrecta',
            ], 401);
        }

        if (!$user->activo) {
            return response()->json([
                'message' => 'Tu cuenta está desactivada. Contacta al administrador.',
            ], 403);
        }

        if ($user->password_changed_at) {
            $dias = \Carbon\Carbon::parse($user->password_changed_at)->diffInDays(now());

            if ($dias >= 90) {
                return response()->json([
                    'message'                 => 'Tu contraseña ha expirado. Debes cambiarla.',
                    'require_password_change' => true,
                    'dias_sin_cambiar'        => $dias,
                ], 403);
            }
        }

        // Sin delete() aquí a propósito: cada login crea un token nuevo sin
        // tocar los de otros dispositivos, para permitir varias sesiones
        // simultáneas del mismo usuario (p. ej. admin en PC y celular a la
        // vez). Sanctum soporta múltiples tokens activos por usuario sin
        // problema. El logout() sigue revocando solo el token actual.
        $token = $user->createToken($this->tokenName($request))->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user'    => new UserResource($user),
            'token'   => $token,
        ]);
    }

    /**
     * Logout
     *
     * Cierra la sesión actual invalidando el token Bearer activo.
     *
     * @authenticated
     *
     * @response 200 {"message": "Sesión cerrada correctamente"}
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente',
        ]);
    }

    /**
     * Actualizar contraseña
     *
     * Cambia la contraseña del usuario autenticado.
     * Invalida todos los tokens anteriores y retorna un nuevo token.
     * La nueva contraseña no puede ser igual a la actual.
     *
     * @authenticated
     *
     * @bodyParam current_password string required Contraseña actual. Example: Admin1234!
     * @bodyParam new_password string required Nueva contraseña mínimo 8 caracteres. Example: NuevoPass123!
     * @bodyParam new_password_confirmation string required Confirmación de nueva contraseña. Example: NuevoPass123!
     *
     * @response 200 scenario="Éxito" {
     *   "message": "Contraseña actualizada correctamente",
     *   "user": {"id": 1, "cedula": "1300000001", "nombre": "Administrador ULEAM"},
     *   "token": "5|newtoken123..."
     * }
     * @response 403 {"error": "La contraseña actual no es correcta"}
     * @response 422 scenario="Misma contraseña" {
     *   "message": "Error de validación",
     *   "errors": {"new_password": ["The new password and current password must be different."]}
     * }
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => [
                'required',
                'confirmed',
                'different:current_password',
                Password::min(8)->mixedCase()->numbers(),
            ],
        ], [
            'new_password.min'       => 'La contraseña debe tener mínimo 8 caracteres.',
            'new_password.mixed'     => 'La contraseña debe combinar mayúsculas y minúsculas.',
            'new_password.numbers'   => 'La contraseña debe incluir al menos un número.',
            'new_password.confirmed' => 'Las contraseñas no coinciden.',
            'new_password.different' => 'La nueva contraseña debe ser distinta a la actual.',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'error' => 'La contraseña actual no es correcta',
            ], 403);
        }

        $user->update([
            'password'            => Hash::make($request->new_password),
            'password_changed_at' => now(),
        ]);

        $user->tokens()->delete();
        $token = $user->createToken($this->tokenName($request))->plainTextToken;

        return response()->json([
            'message' => 'Contraseña actualizada correctamente',
            'user'    => new UserResource($user),
            'token'   => $token,
        ]);
    }

    /**
     * Usuario autenticado
     *
     * Retorna los datos completos del usuario con sesión activa.
     * Útil para Flutter al iniciar la app y verificar el estado de la sesión.
     *
     * @authenticated
     *
     * @response 200 scenario="Éxito" {
     *   "user": {
     *     "id": 1,
     *     "cedula": "1300000001",
     *     "nombre": "Administrador ULEAM",
     *     "email": "admin@uleam.edu.ec",
     *     "activo": true,
     *     "rol": "admin",
     *     "dias_para_vencer": 85,
     *     "miembro_desde": "09/04/2026"
     *   }
     * }
     * @response 401 {"message": "No autenticado"}
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => new UserResource($request->user()->load('roles')),
        ]);
    }

    /**
     * Verificar correo
     *
     * Verifica el correo del usuario autenticado con el código de 6 dígitos enviado por email.
     *
     * @authenticated
     *
     * @bodyParam code string required Código de 6 dígitos. Example: 482931
     *
     * @response 200 {"message": "Correo verificado correctamente.", "user": {}}
     * @response 400 {"error": "El código ha expirado. Solicita uno nuevo."}
     * @response 400 {"error": "Código incorrecto."}
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = auth()->user();

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'El correo ya está verificado.',
            ]);
        }

        if (!$user->email_verification_code) {
            return response()->json([
                'error' => 'No hay código de verificación pendiente.',
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
            'email_verified_at'       => now(),
            'email_verification_code' => null,
            'email_code_expires_at'   => null,
        ])->save();

        return response()->json([
            'message' => 'Correo verificado correctamente.',
            'user'    => new UserResource($user),
        ]);
    }

    /**
     * Reenviar código de verificación
     *
     * Genera y envía un nuevo código de verificación al correo del usuario autenticado.
     *
     * @authenticated
     *
     * @response 200 {"message": "Código de verificación reenviado."}
     * @response 200 {"message": "El correo ya está verificado."}
     */
    public function resendVerification()
    {
        $user = auth()->user();

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'El correo ya está verificado.',
            ]);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'email_verification_code' => $code,
            'email_code_expires_at'   => now()->addMinutes(30),
        ]);

        $user->notify(new VerifyEmailNotification($code));

        return response()->json([
            'message' => 'Código de verificación reenviado.',
        ]);
    }

    /**
     * Nombra el token según la plataforma de origen (header enviado por el
     * cliente Flutter solo cuando corre en web) para que
     * EnsureWebSessionNotIdle sepa a qué tokens aplicarles el límite de
     * inactividad — la app nativa no lo tiene.
     */
    private function tokenName(Request $request): string
    {
        return $request->header('X-Client-Platform') === 'web'
            ? 'auth_token_web'
            : 'auth_token_mobile';
    }
}