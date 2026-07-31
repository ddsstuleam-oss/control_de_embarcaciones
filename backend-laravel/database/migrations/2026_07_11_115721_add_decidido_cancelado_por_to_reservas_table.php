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
        Schema::table('reservas', function (Blueprint $table) {
            // Admin que aprobó o rechazó la reserva (decisión inicial desde "pendiente").
            $table->foreignId('decidido_por_id')->nullable()->constrained('users')->nullOnDelete();
            // Admin que canceló la reserva ya confirmada (acción posterior y separada).
            $table->foreignId('cancelado_por_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('decidido_por_id');
            $table->dropConstrainedForeignId('cancelado_por_id');
        });
    }
};
