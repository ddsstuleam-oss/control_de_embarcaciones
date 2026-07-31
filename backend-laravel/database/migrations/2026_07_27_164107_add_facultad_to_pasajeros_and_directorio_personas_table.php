<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasajeros', function (Blueprint $table) {
            $table->string('facultad')->nullable()->after('carrera');
        });

        Schema::table('directorio_personas', function (Blueprint $table) {
            $table->string('facultad')->nullable()->after('carrera');
        });
    }

    public function down(): void
    {
        Schema::table('pasajeros', function (Blueprint $table) {
            $table->dropColumn('facultad');
        });

        Schema::table('directorio_personas', function (Blueprint $table) {
            $table->dropColumn('facultad');
        });
    }
};
