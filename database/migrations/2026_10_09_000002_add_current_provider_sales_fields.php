<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            if (! Schema::hasColumn('proveedores', 'prov_contacto')) {
                $table->string('prov_contacto', 50)->nullable()->after('direccion');
            }
            if (! Schema::hasColumn('proveedores', 'rubro')) {
                $table->string('rubro', 100)->nullable()->after('email');
            }
        });

        Schema::table('ventas', function (Blueprint $table) {
            if (! Schema::hasColumn('ventas', 'ot_codigo')) {
                $table->unsignedBigInteger('ot_codigo')->nullable()->after('cliente_id');
            }
        });
    }

    public function down(): void
    {
        foreach ([['proveedores', ['prov_contacto', 'rubro']], ['ventas', ['ot_codigo']]] as [$tableName, $columns]) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns) {
                foreach ($columns as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
