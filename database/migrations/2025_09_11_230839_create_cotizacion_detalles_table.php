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
       if (Schema::hasTable('cotizaciones') && !Schema::hasTable('cotizacion_detalles')) {
           Schema::create('cotizacion_detalles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cotizacion_id');
                $table->unsignedBigInteger('producto_id')->nullable();
                $table->string('descripcion');
                $table->integer('cantidad');
                $table->decimal('precio_unitario', 10, 2);
                $table->decimal('subtotal', 10, 2);
                $table->timestamps();

                $table->foreign('cotizacion_id')->references('id')->on('cotizaciones')->onDelete('cascade');
                $table->foreign('producto_id')->references('id')->on('productos')->onDelete('set null');
           });
       }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacion_detalles');
    }
};
