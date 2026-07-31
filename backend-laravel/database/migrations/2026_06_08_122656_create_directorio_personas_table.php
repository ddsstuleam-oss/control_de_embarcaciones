<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('directorio_personas', function (Blueprint $table) {
            $table->id();
            $table->string('cedula', 10)->unique();
            $table->string('nombre');
            $table->enum('tipo', ['estudiante', 'docente', 'administrativo', 'externo'])->default('externo');
            $table->string('carrera')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('directorio_personas');
    }
};