<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
    

Schedule::command('reservas:recordatorios')->daily('08:00');
Schedule::command('boletos:marcar-vencidos')->everyFiveMinutes();
Schedule::command('reservas:marcar-vencidas')->everyFiveMinutes();
Schedule::command('viajes:verificar-retrasos')->everyFiveMinutes();
Schedule::command('queue:work --stop-when-empty')->everyMinute();
