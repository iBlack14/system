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
        Schema::table('cotizacion_detalles', function (Blueprint $table) {
        $table->enum('categoria', [
            'servicio',
            'mano_obra',
            'material',
            'equipo',
            'transporte'
        ])->default('servicio')->after('total');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotizacion_detalles', function (Blueprint $table) {
            //
        });
    }
};
