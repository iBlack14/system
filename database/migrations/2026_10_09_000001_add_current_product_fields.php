<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (! Schema::hasColumn('productos', 'costo_unitario')) {
                $table->decimal('costo_unitario', 10, 2)->nullable()->after('nombre');
            }
            if (! Schema::hasColumn('productos', 'precio_venta')) {
                $table->decimal('precio_venta', 10, 2)->nullable()->after('precio_unitario');
            }
            if (! Schema::hasColumn('productos', 'utilidad')) {
                $table->decimal('utilidad', 10, 2)->nullable()->after('precio_venta');
            }
            if (! Schema::hasColumn('productos', 'almacen')) {
                $table->string('almacen', 100)->nullable()->after('utilidad');
            }
            if (! Schema::hasColumn('productos', 'ubicacion_columna')) {
                $table->string('ubicacion_columna', 50)->nullable()->after('almacen');
            }
            if (! Schema::hasColumn('productos', 'ubicacion_fila')) {
                $table->string('ubicacion_fila', 50)->nullable()->after('ubicacion_columna');
            }
            if (! Schema::hasColumn('productos', 'porcentaje5')) {
                $table->decimal('porcentaje5', 8, 2)->nullable()->after('ubicacion_fila');
            }
            if (! Schema::hasColumn('productos', 'porcentaje10')) {
                $table->decimal('porcentaje10', 8, 2)->nullable()->after('porcentaje5');
            }
            if (! Schema::hasColumn('productos', 'porcentaje50')) {
                $table->decimal('porcentaje50', 8, 2)->nullable()->after('porcentaje10');
            }
            if (! Schema::hasColumn('productos', 'porcentaje100')) {
                $table->decimal('porcentaje100', 8, 2)->nullable()->after('porcentaje50');
            }
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            foreach (['costo_unitario', 'precio_venta', 'utilidad', 'almacen', 'ubicacion_columna', 'ubicacion_fila', 'porcentaje5', 'porcentaje10', 'porcentaje50', 'porcentaje100'] as $column) {
                if (Schema::hasColumn('productos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
