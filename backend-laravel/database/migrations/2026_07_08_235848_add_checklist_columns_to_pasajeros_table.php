<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasajeros', function (Blueprint $table) {
            $table->timestamp('hora_abordaje')->nullable();
            $table->text('observaciones_embarque')->nullable();
            $table->boolean('llego')->nullable();
            $table->timestamp('hora_llegada')->nullable();
            $table->text('observaciones_llegada')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pasajeros', function (Blueprint $table) {
            $table->dropColumn([
                'hora_abordaje',
                'observaciones_embarque',
                'llego',
                'hora_llegada',
                'observaciones_llegada',
            ]);
        });
    }
};
