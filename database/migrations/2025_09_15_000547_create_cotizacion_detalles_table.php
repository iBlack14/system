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
       Schema::create('cotizacion_detalles', function (Blueprint $table) {
        $table->bigIncrements('id');

        $table->unsignedBigInteger('cotizacion_id');
        $table->unsignedBigInteger('producto_id');

        $table->integer('cantidad');
        $table->decimal('valor_unitario', 10, 2);
        $table->decimal('porcentaje_impuesto', 5, 2);
        $table->decimal('impuesto', 10, 2);
        $table->decimal('precio_unitario', 10, 2);
        $table->decimal('total', 12, 2);

        $table->enum('categoria', [
            'servicio',
            'mano_obra',
            'material',
            'equipo',
            'transporte'
        ])->default('servicio');

        $table->timestamps();

        $table->foreign('cotizacion_id')
              ->references('id')->on('cotizaciones')
              ->onDelete('cascade');

        $table->foreign('producto_id')
              ->references('id')->on('productos')
              ->onDelete('restrict');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacion_detalles');
    }
};
