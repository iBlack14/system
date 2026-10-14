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
       Schema::create('ordenes_trabajo', function (Blueprint $table) {
            $table->id(); // código interno autoincremental
            $table->string('codigo_ot_cliente')->unique(); // código de orden de trabajo del cliente
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('tecnico_id');
            $table->unsignedBigInteger('user_id'); // usuario que la generó
            $table->date('fecha');
            $table->string('estado')->default('pendiente'); // pendiente, en proceso, finalizado
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // 🔗 Relaciones
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->foreign('tecnico_id')->references('id')->on('tecnicos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes_trabajo');
    }
};
