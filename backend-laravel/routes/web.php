<?php

use App\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Sirve las imágenes públicas (fotos de perfil, embarcaciones, evidencias)
// con cabecera CORS — necesario para que Flutter Web pueda cargarlas.
Route::get('/media/{path}', [MediaController::class, 'show'])
    ->where('path', '.*')
    ->name('media.show');
