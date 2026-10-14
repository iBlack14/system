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
        Schema::table('tecnicos', function (Blueprint $table) {
        $table->tinyInteger('activo')->default(1)->after('nombre'); // Cambia 'nombre' por el campo después del cual quieras ponerlo
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::table('tecnicos', function (Blueprint $table) {
        $table->dropColumn('activo');
    });
    }
};
