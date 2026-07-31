<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('viajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->unique()->constrained('reservas')->cascadeOnDelete();
            $table->foreignId('embarcacion_id')->constrained('embarcaciones');
            $table->foreignId('operador_id')->constrained('users');
            $table->enum('estado', ['abordando', 'en_curso', 'finalizado'])->default('abordando');
            $table->dateTime('hora_programada_salida');
            $table->dateTime('hora_programada_llegada');
            $table->dateTime('hora_real_salida')->nullable();
            $table->dateTime('hora_real_llegada')->nullable();
            $table->foreignId('usuario_cierre_id')->nullable()->constrained('users');
            $table->text('observaciones_cierre')->nullable();
            $table->dateTime('fecha_finalizacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('viajes');
    }
};
