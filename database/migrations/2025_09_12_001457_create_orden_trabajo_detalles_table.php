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
        Schema::create('orden_trabajo_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orden_trabajo_id');
            $table->unsignedBigInteger('producto_id')->nullable(); // opcional, si trabajas con inventario
            $table->string('actividad')->nullable(); // descripción de la tarea realizada
            $table->integer('cantidad')->default(1); // repuestos, horas, etc.
            $table->decimal('costo_unitario', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamps();

            // 🔗 Relaciones
            $table->foreign('orden_trabajo_id')->references('id')->on('ordenes_trabajo')->onDelete('cascade');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_trabajo_detalles');
    }
};
