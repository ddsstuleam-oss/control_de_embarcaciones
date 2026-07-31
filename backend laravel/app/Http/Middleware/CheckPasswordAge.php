<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckPasswordAge
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->password_changed_at) {
            $days = Carbon::parse($user->password_changed_at)->diffInDays(now());

            if ($days >= 90) {
                return response()->json([
                    'message' => 'Tu contraseña ha expirado. Debes cambiarla para continuar.',
                    'require_password_change' => true
                ], 403);
            }
        }

        return $next($request);
    }
}

