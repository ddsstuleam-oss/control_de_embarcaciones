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
            WHERE conrelid = 'boletos'::regclass
              AND contype = 'c'
              AND pg_get_constraintdef(oid) LIKE '%estado%'
        ");

        if ($constraint) {
            DB::statement("ALTER TABLE boletos DROP CONSTRAINT {$constraint->conname}");
        }

        DB::statement("UPDATE boletos SET estado = 'vencido' WHERE estado = 'no_show'");

        DB::statement("ALTER TABLE boletos ADD CONSTRAINT boletos_estado_check CHECK (estado IN ('valido', 'usado', 'cancelado', 'vencido'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE boletos DROP CONSTRAINT IF EXISTS boletos_estado_check");

        DB::statement("UPDATE boletos SET estado = 'no_show' WHERE estado = 'vencido'");

        DB::statement("ALTER TABLE boletos ADD CONSTRAINT boletos_estado_check CHECK (estado IN ('valido', 'usado', 'cancelado', 'no_show'))");
    }
};
