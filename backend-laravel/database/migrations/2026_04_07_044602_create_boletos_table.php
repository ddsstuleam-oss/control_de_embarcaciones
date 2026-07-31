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
    Schema::create('boletos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('reserva_id')->constrained()->cascadeOnDelete();
        $table->string('codigo_qr')->unique();
        $table->string('pdf_url')->nullable();
        $table->enum('estado', ['valido', 'usado', 'cancelado'])->default('valido');
        $table->timestamp('usado_en')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boletos');
    }
};
