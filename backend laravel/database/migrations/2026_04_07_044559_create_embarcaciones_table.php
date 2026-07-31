<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('embarcaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('capacidad');
            $table->enum('estado', ['disponible', 'mantenimiento'])->default('disponible');
            $table->text('descripcion')->nullable();
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('embarcaciones');
    }
};