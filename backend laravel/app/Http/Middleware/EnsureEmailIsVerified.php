<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->email_verified_at) {
            return response()->json([
                'error'                 => 'Debes verificar tu correo electrónico.',
                'requires_verification' => true,
            ], 403);
        }

        return $next($request);
    }
}