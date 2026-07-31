<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $constraint = DB::selectOne("
            SELECT conname FROM pg_constraint
            WHERE conrelid = 'reservas'::regclass
              AND contype = 'c'
              AND pg_get_constraintdef(oid) LIKE '%estado%'
        ");

        if ($constraint) {
            DB::statement("ALTER TABLE reservas DROP CONSTRAINT {$constraint->conname}");
        }

        DB::statement("UPDATE reservas SET estado = 'vencida' WHERE estado = 'no_presentado'");

        DB::statement("ALTER TABLE reservas ADD CONSTRAINT reservas_estado_check CHECK (estado IN ('pendiente', 'confirmada', 'cancelada', 'completada', 'vencida', 'rechazada'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE reservas DROP CONSTRAINT IF EXISTS reservas_estado_check");

        DB::statement("UPDATE reservas SET estado = 'no_presentado' WHERE estado = 'vencida'");

        DB::statement("ALTER TABLE reservas ADD CONSTRAINT reservas_estado_check CHECK (estado IN ('pendiente', 'confirmada', 'cancelada', 'completada', 'no_presentado', 'rechazada'))");
    }
};
