<?php

namespace App\Console\Commands;

use App\Mail\AlertaRetrasoViaje;
use App\Models\User;
use App\Models\Viaje;
use App\Models\ViajeAlerta;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class VerificarRetrasosViajes extends Command
{
    protected $signature   = 'viajes:verificar-retrasos';
    protected $description = 'Detecta viajes en curso con más de 60 minutos de retraso sin llegada registrada y alerta a los administradores';

    public function handle(): void
    {
        $viajes = Viaje::with('embarcacion', 'reserva.user', 'operador')
            ->where('estado', 'en_curso')
            ->whereNull('hora_real_llegada')
            ->where('hora_programada_llegada', '<', now()->subMinutes(60))
            ->whereDoesntHave('alertas', fn ($q) => $q->where('tipo', 'retraso_llegada'))
            ->get();

        $this->info("Viajes con retraso detectados: {$viajes->count()}");

        $admins = User::role('admin')->get();

        foreach ($viajes as $viaje) {
            ViajeAlerta::create([
                'viaje_id' => $viaje->id,
                'tipo'     => 'retraso_llegada',
                'mensaje'  => 'El viaje #' . $viaje->id . ' de la embarcación ' . $viaje->embarcacion->nombre .
                              ' superó los 60 minutos de retraso sobre su hora programada de llegada (' .
                              $viaje->hora_programada_llegada->format('d/m/Y H:i') . ') sin registrar llegada real.',
            ]);

            foreach ($admins as $admin) {
                try {
                    Mail::to($admin->email)->queue(new AlertaRetrasoViaje($viaje));
                } catch (\Exception $e) {
                    $this->error("Error notificando a {$admin->email}: {$e->getMessage()}");
                }
            }

            $this->info("Alerta registrada para viaje #{$viaje->id}");
        }
    }
}
