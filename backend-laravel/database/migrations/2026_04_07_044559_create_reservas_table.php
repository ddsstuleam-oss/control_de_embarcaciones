<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('embarcacion_id')
                  ->constrained('embarcaciones')
                  ->cascadeOnDelete();

            $table->date('fecha');
            $table->integer('total_personas');

            $table->enum('estado', [
                'confirmada',
                'cancelada',
                'completada',
                'no_presentado',
            ])->default('confirmada');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};