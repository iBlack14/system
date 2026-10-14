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
       Schema::create('cotizaciones', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Relaciones (FK)
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ot_codigo');

            // Datos principales
            $table->string('forma_pago', 7);
            $table->date('fecha');
            // validez NO NULO, por defecto 15 días (puedes cambiar el default si lo deseas)
            $table->string('validez', 255)->default('15');
            $table->decimal('total', 10, 2);
            $table->string('estado', 255)->default('pendiente');
            $table->text('observaciones')->nullable();

            // Operaciones tributarias NO NULAS (por defecto 0.00)
            $table->decimal('op_gravada', 8, 2)->default(0.00);
            $table->decimal('op_exonerada', 8, 2)->default(0.00);
            $table->decimal('op_inafecta', 8, 2)->default(0.00);
            $table->decimal('impuesto', 8, 2)->default(0.00);

            // Timestamps
            $table->timestamps();

            // Índices y claves foráneas
            $table->index('cliente_id');
            $table->index('user_id');
            $table->index('ot_codigo');

            $table->foreign('cliente_id')
                ->references('id')->on('clientes')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            // OJO: ajusta el nombre de la tabla de órdenes si en tu proyecto es distinto.
            // Aquí se asume que la tabla de órdenes se llama 'ordenes_trabajo'
            $table->foreign('ot_codigo')
                ->references('id')->on('ordenes_trabajo')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
