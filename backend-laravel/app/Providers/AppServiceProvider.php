<?php

namespace App\Providers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Laravel\Sanctum\Events\TokenAuthenticated;
use App\Models\Reserva;
use App\Models\Boleto;
use App\Models\User;
use App\Observers\ReservaObserver;
use App\Observers\BoletoObserver;
use App\Observers\UserObserver;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}
    

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        Carbon::setLocale('es');

        Carbon::setLocale(config('app.locale'));


                // Observers
        Reserva::observe(ReservaObserver::class);
        Boleto::observe(BoletoObserver::class);
        User::observe(UserObserver::class);

        // Cierra sesiones web inactivas por más de 30 min. Se engancha en
        // TokenAuthenticated (que Sanctum dispara ANTES de pisar
        // last_used_at con la hora actual — ver Guard::__invoke) en vez de
        // un middleware propio: probamos con uno antes de auth:sanctum en
        // la ruta y Laravel igual lo reordenaba después según su lista de
        // prioridad interna, así que siempre veíamos la marca ya
        // actualizada por la petición en curso. Solo afecta tokens '_web'
        // (ver AuthController::tokenName) — la app nativa no tiene este
        // límite, confía en el bloqueo del propio dispositivo.
        Event::listen(TokenAuthenticated::class, function (TokenAuthenticated $event) {
            $token = $event->token;

            if (!str_ends_with((string) $token->name, '_web')) {
                return;
            }

            $ultimaActividad = $token->last_used_at ?? $token->created_at;

            if ($ultimaActividad->diffInMinutes(now()) > 30) {
                $token->delete();
                throw new AuthenticationException('Tu sesión expiró por inactividad.');
            }
        });
    }
}