<?php

namespace App\Console\Commands;

use App\Models\Reserva;
use Illuminate\Console\Command;

class MarcarReservasVencidas extends Command
{
    protected $signature   = 'reservas:marcar-vencidas';
    protected $description = 'Marca como vencidas las reservas pendientes cuya hora de viaje ya pasó sin ser aprobadas ni rechazadas';

    public function handle(): void
    {
        $reservas = Reserva::where('estado', 'pendiente')
            ->whereDate('fecha', '<=', now()->toDateString())
            ->get()
            ->filter(fn (Reserva $reserva) => $reserva->estaVencidaPendiente());

        foreach ($reservas as $reserva) {
            $reserva->marcarVencida();
        }

        $this->info("Reservas pendientes marcadas como vencidas: {$reservas->count()}");
    }
}
