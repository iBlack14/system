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
        Schema::create('tecnicos', function (Blueprint $table) {
            $table->id(); // Crea una columna ID autoincremental
            $table->string('nombre'); // Columna para el nombre
            $table->string('email')->unique(); // Columna para el email, con índice único
            $table->string('telefono')->nullable(); // Columna para el teléfono, puede ser nulo
            $table->timestamps(); // Agrega las columnas 'created_at' y 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tecnicos');
    }
};
