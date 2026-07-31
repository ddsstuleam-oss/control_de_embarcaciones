<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * Sirve archivos del disco "public" (storage/app/public) con cabecera
     * CORS explícita. El symlink public/storage los sirve como estáticos
     * sin pasar por Laravel, así que ningún middleware de CORS les aplica
     * — y Flutter Web (renderer CanvasKit) necesita ese header para poder
     * leer los bytes de la imagen vía fetch() y decodificarla.
     */
    public function show(string $path)
    {
        $normalizado = str_replace('\\', '/', $path);

        if (str_contains($normalizado, '..') || ! Storage::disk('public')->exists($normalizado)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($normalizado), [
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control'               => 'public, max-age=86400',
        ]);
    }
}
