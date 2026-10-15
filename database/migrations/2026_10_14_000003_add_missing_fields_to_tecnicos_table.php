<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tecnicos', function (Blueprint $table) {
            if (!Schema::hasColumn('tecnicos', 'dni')) {
                $table->string('dni', 20)->nullable()->after('id');
            }
            if (!Schema::hasColumn('tecnicos', 'department_id')) {
                $table->string('department_id', 10)->nullable();
            }
            if (!Schema::hasColumn('tecnicos', 'province_id')) {
                $table->string('province_id', 10)->nullable();
            }
            if (!Schema::hasColumn('tecnicos', 'districts_id')) {
                $table->string('districts_id', 10)->nullable();
            }
            if (!Schema::hasColumn('tecnicos', 'profesion')) {
                $table->string('profesion', 100)->nullable();
            }
            if (!Schema::hasColumn('tecnicos', 'puesto')) {
                $table->string('puesto', 100)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tecnicos', function (Blueprint $table) {
            $table->dropColumn(['dni', 'department_id', 'province_id', 'districts_id', 'profesion', 'puesto']);
        });
    }
};
