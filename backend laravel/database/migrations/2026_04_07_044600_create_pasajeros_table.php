<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('pasajeros', function (Blueprint $table) {
        $table->id();
        $table->foreignId('reserva_id')
              ->constrained()
              ->cascadeOnDelete();

        // Obligatorios
        $table->string('nombre');
        $table->string('cedula');

        // Opcionales
        $table->enum('tipo', [
            'estudiante',
            'docente',
            'administrativo',
            'externo',
        ])->default('externo');

        $table->string('carrera')->nullable(); // solo estudiantes
        $table->string('telefono', 20)->nullable();
        $table->string('email')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasajeros');
    }
};
