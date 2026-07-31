<?php

namespace App\Console\Commands;

use App\Models\Boleto;
use Illuminate\Console\Command;

class MarcarBoletosVencidos extends Command
{
    protected $signature   = 'boletos:marcar-vencidos';
    protected $description = 'Marca como vencidos los boletos válidos cuya ventana de embarque ya expiró';

    public function handle(): void
    {
        // <= hoy (no solo "hoy"): también atrapa boletos de días anteriores
        // que hayan quedado sin procesar si el scheduler no corrió a tiempo.
        $boletos = Boleto::with('reserva')
            ->where('estado', 'valido')
            ->whereHas('reserva', fn ($q) => $q->whereDate('fecha', '<=', now()->toDateString()))
            ->get()
            ->filter(fn (Boleto $boleto) => $boleto->estaVencido());

        foreach ($boletos as $boleto) {
            $boleto->marcarVencido();
        }

        $this->info("Boletos marcados como vencidos (y sus reservas): {$boletos->count()}");
    }
}
