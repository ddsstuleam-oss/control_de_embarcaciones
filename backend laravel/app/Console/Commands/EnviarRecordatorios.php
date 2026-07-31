<?php

namespace App\Console\Commands;

use App\Mail\RecordatorioReserva;
use App\Models\Reserva;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarRecordatorios extends Command
{
    protected $signature   = 'reservas:recordatorios';
    protected $description = 'Envía recordatorios de reservas para mañana';

    public function handle(): void
    {
        $manana = now()->addDay()->toDateString();

        $reservas = Reserva::with('user', 'embarcacion', 'boleto', 'pasajeros')
            ->where('fecha', $manana)
            ->where('estado', 'confirmada')
            ->get();

        $this->info("Enviando recordatorios para {$reservas->count()} reservas...");

        foreach ($reservas as $reserva) {
            try {
                Mail::to($reserva->user->email)
                    ->queue(new RecordatorioReserva($reserva));

                $this->info("Enviado a: {$reserva->user->email}");
            } catch (\Exception $e) {
                $this->error("Error con reserva #{$reserva->id}: {$e->getMessage()}");
            }
        }

        $this->info('Recordatorios enviados correctamente.');
    }
}