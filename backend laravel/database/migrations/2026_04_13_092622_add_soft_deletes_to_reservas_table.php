<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('reservas', function (Blueprint $table) {
        $table->softDeletes(); // Agrega la columna deleted_at
    });
}

public function down(): void
{
    Schema::table('reservas', function (Blueprint $table) {
        $table->dropSoftDeletes();
    });
}
};
