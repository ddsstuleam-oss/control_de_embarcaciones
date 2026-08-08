<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Validation\Rules\Password;

/**
 * @group Recuperación de contraseña
 *
 * Endpoints para solicitar y restablecer contraseña olvidada.
 * El flujo es: solicitar token por email → recibir código de 6 dígitos → restablecer contraseña.
 * El token expira en 60 minutos.
 */
class ForgotPasswordController extends Controller
{
    /**
     * Solicitar recuperación de contraseña
     *
     * Envía un código de 6 dígitos al correo registrado para restablecer la contraseña.
     * El código expira en 60 minutos.
     * Si ya existe un token anterior para ese email, se elimina y se genera uno nuevo.
     *
     * @bodyParam email string required Correo electrónico registrado en el sistema. Example: estudiante@uleam.edu.ec
     *
     * @response 200 {"message": "Correo de recuperación enviado."}
     * @response 404 {"message": "El correo no está registrado."}
     * @response 422 scenario="Validación" {
     *   "message": "Error de validación",
     *   "errors": {"email": ["The email field is required."]}
     * }
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'El correo no está registrado.'], 404);
        }

        DB::table('password_resets')->where('email', $request->email)->delete();

        $plainToken  = Str::random(6);
        $hashedToken = Hash::make($plainToken);

        DB::table('password_resets')->insert([
            'email'      => $request->email,
            'token'      => $hashedToken,
            'created_at' => Carbon::now(),
        ]);

        $user->notify(new PasswordResetNotification($plainToken));

        return response()->json(['message' => 'Correo de recuperación enviado.']);
    }

    /**
     * Restablecer contraseña
     *
     * Restablece la contraseña usando el código de 6 dígitos recibido por email.
     * El token es de un solo uso — se elimina al ser utilizado.
     * El token expira a los 60 minutos de haber sido generado.
     *
     * @bodyParam email string required Correo electrónico registrado. Example: estudiante@uleam.edu.ec
     * @bodyParam token string required Código de 6 dígitos recibido por email. Example: A3F9K2
     * @bodyParam password string required Nueva contraseña mínimo 6 caracteres. Example: NuevoPass123!
     * @bodyParam password_confirmation string required Confirmación de nueva contraseña. Example: NuevoPass123!
     *
     * @response 200 {"message": "Contraseña actualizada correctamente."}
     * @response 400 scenario="Token inválido" {"message": "Token inválido."}
     * @response 400 scenario="Token expirado" {"message": "El token ha expirado."}
     * @response 404 {"message": "Usuario no encontrado."}
     * @response 422 scenario="Validación" {
     *   "message": "Error de validación",
     *   "errors": {"password": ["The password field confirmation does not match."]}
     * }
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'token'    => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'password.min'       => 'La contraseña debe tener mínimo 8 caracteres.',
            'password.mixed'     => 'La contraseña debe combinar mayúsculas y minúsculas.',
            'password.numbers'   => 'La contraseña debe incluir al menos un número.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$reset || !Hash::check($request->token, $reset->token)) {
            return response()->json(['message' => 'Token inválido.'], 400);
        }

        if (Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            return response()->json(['message' => 'El token ha expirado.'], 400);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Contraseña actualizada correctamente.']);
    }
}