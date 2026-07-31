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
    Schema::table('boletos', function (Blueprint $table) {
        $table->softDeletes();
    });
}

public function down(): void
{
    Schema::table('boletos', function (Blueprint $table) {
        $table->dropSoftDeletes();
    });
}

};
