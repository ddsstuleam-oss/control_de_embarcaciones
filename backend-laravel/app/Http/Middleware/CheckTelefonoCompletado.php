<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTelefonoCompletado
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && empty($user->telefono)) {
            return response()->json([
                'message' => 'Debes completar tu número de teléfono antes de reservar.',
                'require_telefono' => true,
            ], 403);
        }

        return $next($request);
    }
}
