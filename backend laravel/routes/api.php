<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;

use App\Http\Controllers\EmbarcacionController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\BoletoController;
use App\Http\Controllers\PasajeroController;
use App\Http\Controllers\ViajeController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ActividadController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\DirectorioController;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::post('/register',        [AuthController::class, 'register']);

// Login con rate limit: máximo 5 intentos por minuto
Route::middleware('throttle:login')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password',  [ForgotPasswordController::class, 'resetPassword']);

// Consulta pública sin login
Route::get('/embarcaciones',  [EmbarcacionController::class, 'index']);
Route::get('/disponibilidad', [EmbarcacionController::class, 'disponibilidad']);

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS — solo autenticado (sin verificación requerida)
| Necesarias para completar el flujo de verificación de email
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',        [AuthController::class, 'logout']);
    Route::get('/me',             [AuthController::class, 'me']);
    Route::get('/user', fn(Request $r) => response()->json($r->user()));
    Route::post('/email/verify',  [AuthController::class, 'verifyEmail']);
    Route::post('/email/resend',  [AuthController::class, 'resendVerification']);
});

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS — email verificado + password.age
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'verified', 'password.age'])->group(function () {

    /*
    |--- AUTH ---------------------------------------------------------------
    */
    Route::post('/update-password', [AuthController::class, 'updatePassword']);

    /*
    |--- PERFIL -------------------------------------------------------------
    */
    Route::get('/perfil',             [PerfilController::class, 'index']);
    Route::put('/perfil',             [PerfilController::class, 'update']);
    Route::post('/perfil/foto',       [PerfilController::class, 'subirFoto']);
    Route::get('/perfil/reservas',    [PerfilController::class, 'reservas']);
    Route::get('/perfil/actividades', [PerfilController::class, 'actividades']);
    Route::post('/actividad',         [ActividadController::class, 'store']);

    /*
    |--- EMBARCACIONES (usuario) --------------------------------------------
    */
    Route::get('/embarcaciones/{id}',          [EmbarcacionController::class, 'show']);
    Route::get('/embarcaciones/{id}/horarios', [EmbarcacionController::class, 'horariosOcupados']);

    /*
    |--- RESERVAS (usuario) -------------------------------------------------
    */
    Route::get('/reservas',                 [ReservaController::class, 'index']);
    Route::post('/reservas',                [ReservaController::class, 'store'])->middleware('telefono.required');
    Route::get('/reservas/{id}',            [ReservaController::class, 'show']);
    Route::get('/reservas-fecha',           [ReservaController::class, 'porFecha']);
    Route::patch('/reservas/{id}/cancelar', [ReservaController::class, 'cancelar']);

    /*
    |--- OPERADOR — validación en puerto ------------------------------------
    */
    Route::middleware('role:operador|admin')->group(function () {
        Route::post('/validar-boleto/{codigo}', [BoletoController::class, 'validar']);
        Route::patch('/boletos/{id}/embarcar',  [BoletoController::class, 'embarcar']);
        Route::get('/reservas-hoy',              [ReservaController::class, 'hoy']);

        // Edición de pasajeros al momento del embarque (reserva ya confirmada)
        Route::post('/reservas/{reserva}/pasajeros',              [PasajeroController::class, 'store']);
        Route::put('/reservas/{reserva}/pasajeros/{pasajero}',    [PasajeroController::class, 'update']);
        Route::delete('/reservas/{reserva}/pasajeros/{pasajero}', [PasajeroController::class, 'destroy']);

        // Control Operativo del Viaje
        Route::post('/viajes/escanear/{codigo}',        [ViajeController::class, 'escanear']);
        Route::get('/viajes-activos',                   [ViajeController::class, 'activos']);
        Route::get('/viajes/{id}',                      [ViajeController::class, 'show']);
        Route::patch('/viajes/{id}/checklist-embarque', [ViajeController::class, 'checklistEmbarque']);
        Route::post('/viajes/{id}/evidencias',          [ViajeController::class, 'subirEvidencia']);
        Route::delete('/viajes/{id}/evidencias/{evidencia}', [ViajeController::class, 'eliminarEvidencia']);
        Route::patch('/viajes/{id}/iniciar',            [ViajeController::class, 'iniciar']);
        Route::patch('/viajes/{id}/checklist-llegada',  [ViajeController::class, 'checklistLlegada']);
        Route::patch('/viajes/{id}/registrar-llegada',  [ViajeController::class, 'registrarLlegada']);
        Route::patch('/viajes/{id}/finalizar',          [ViajeController::class, 'finalizar']);
        Route::get('/viajes/{id}/reporte-pdf',          [ViajeController::class, 'reportePdf']);
    });

    /*
    |--- BOLETOS (usuario) --------------------------------------------------
    */
    Route::get('/boletos/{id}',     [BoletoController::class, 'show']);
    Route::get('/boletos/{id}/pdf', [BoletoController::class, 'generarPDF']);

    /*
    |--- DIRECTORIO (usuario autenticado) -----------------------------------
    */
    Route::get('/directorio/buscar', [DirectorioController::class, 'buscar']);

    /*
    |--- ADMIN --------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {

        // Dashboard y estadísticas
        Route::get('/admin/dashboard',    [DashboardController::class, 'index']);
        Route::get('/admin/estadisticas', [DashboardController::class, 'estadisticas']);

        // Reportes
        Route::get('/admin/reportes/excel-pasajeros',  [ReporteController::class, 'excelPasajeros']);
        Route::get('/admin/reportes/excel-reservas',   [ReporteController::class, 'excelReservas']);
        Route::get('/admin/reportes/pdf-manifiesto',   [ReporteController::class, 'pdfManifiesto']);

        // Usuarios
        Route::get('/admin/usuarios',               [UsuarioController::class, 'index']);
        Route::post('/admin/usuarios',              [UsuarioController::class, 'store']);
        Route::get('/admin/usuarios/{id}',          [UsuarioController::class, 'show']);
        Route::patch('/admin/usuarios/{id}/toggle', [UsuarioController::class, 'toggle']);
        Route::patch('/admin/usuarios/{id}/rol',    [UsuarioController::class, 'cambiarRol']);
        Route::delete('/admin/usuarios/{id}',       [UsuarioController::class, 'destroy']);

        // Embarcaciones CRUD admin
        Route::post('/admin/embarcaciones',         [EmbarcacionController::class, 'store']);
        Route::put('/admin/embarcaciones/{id}',     [EmbarcacionController::class, 'update']);
        Route::delete('/admin/embarcaciones/{id}',  [EmbarcacionController::class, 'destroy']);

        // Directorio
        Route::get('/admin/directorio',           [DirectorioController::class, 'index']);
        Route::post('/admin/directorio',          [DirectorioController::class, 'store']);
        Route::post('/admin/directorio/importar', [DirectorioController::class, 'importar']);
        Route::get('/admin/directorio/plantilla', [DirectorioController::class, 'plantilla']);
        Route::delete('/admin/directorio/{id}',   [DirectorioController::class, 'destroy']);

        // Reservas admin
        Route::get('/admin/reservas',                      [ReservaController::class, 'adminIndex']);
        Route::patch('/admin/reservas/{id}/aprobar',       [ReservaController::class, 'adminAprobar']);
        Route::patch('/admin/reservas/{id}/rechazar',      [ReservaController::class, 'adminRechazar']);
        Route::patch('/admin/reservas/{id}/cancelar',      [ReservaController::class, 'adminCancelar']);
        Route::delete('/admin/reservas/{id}',              [ReservaController::class, 'adminDestroy']);
        Route::patch('/admin/reservas/{id}/restore',       [ReservaController::class, 'adminRestore']);
    });
});

/*
|--------------------------------------------------------------------------
| DEPLOY WEBHOOK — recibido por GitHub en cada push a main
|--------------------------------------------------------------------------
*/

Route::post('/deploy-hook-b917e2', function (Request $request) {
    $secret    = env('DEPLOY_WEBHOOK_SECRET');
    $payload   = $request->getContent();
    $signature = $request->header('X-Hub-Signature-256', '');
    $expected  = 'sha256=' . hash_hmac('sha256', $payload, (string) $secret);

    if (!$secret || !hash_equals($expected, $signature)) {
        abort(403, 'Invalid signature');
    }

    $data = json_decode($payload, true);
    if (($data['ref'] ?? '') !== 'refs/heads/main') {
        return response('Ignored: not main branch', 200);
    }

    $repoPath = base_path();

    // HOME no está seteado cuando este comando corre desde una petición web
    // (PHP-FPM no lo hereda como una sesión SSH); sin esto, composer falla.
    $cmd = 'export HOME=/home/uleamemb && cd ' . escapeshellarg($repoPath) . ' && '
         . '/usr/bin/git pull origin main 2>&1 && '
         . '/usr/local/bin/composer install --optimize-autoloader --no-interaction 2>&1 && '
         . '/usr/local/bin/php artisan migrate --force 2>&1 && '
         . '/usr/local/bin/php artisan config:clear 2>&1 && '
         . '/usr/local/bin/php artisan route:clear 2>&1 && '
         . '/usr/local/bin/php artisan cache:clear 2>&1';

    $output = shell_exec($cmd);
    \Illuminate\Support\Facades\Log::info("[DEPLOY]\n" . $output);

    return response('OK', 200);
});
