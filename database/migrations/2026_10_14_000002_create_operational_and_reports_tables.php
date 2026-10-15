<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Sedes
        if (!Schema::hasTable('sedes')) {
            Schema::create('sedes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cliente_id')->nullable();
                $table->string('nombre');
                $table->string('direccion')->nullable();
                $table->string('telefono')->nullable();
                $table->boolean('activo')->default(true);
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        // 2. Responsables / Solicitantes
        if (!Schema::hasTable('responsables')) {
            Schema::create('responsables', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cliente_id')->nullable();
                $table->string('nombre');
                $table->string('cargo')->nullable();
                $table->string('email')->nullable();
                $table->string('telefono')->nullable();
                $table->boolean('activo')->default(true);
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamps();
            });
        }

        // 3. Catálogo de Estados OT
        if (!Schema::hasTable('orden_trabajo_estado_catalogo')) {
            Schema::create('orden_trabajo_estado_catalogo', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('color')->default('#6c757d');
                $table->integer('orden')->default(1);
                $table->string('color_texto')->default('#ffffff');
            });

            DB::table('orden_trabajo_estado_catalogo')->insert([
                ['id' => 1, 'nombre' => 'Pendiente / Inicial', 'color' => '#f59e0b', 'orden' => 1, 'color_texto' => '#ffffff'],
                ['id' => 2, 'nombre' => 'En Ejecución', 'color' => '#3b82f6', 'orden' => 2, 'color_texto' => '#ffffff'],
                ['id' => 3, 'nombre' => 'Documentación', 'color' => '#8b5cf6', 'orden' => 3, 'color_texto' => '#ffffff'],
                ['id' => 4, 'nombre' => 'Finalizado / Inf. Final', 'color' => '#10b981', 'orden' => 4, 'color_texto' => '#ffffff'],
                ['id' => 5, 'nombre' => 'Facturación', 'color' => '#06b6d4', 'orden' => 5, 'color_texto' => '#ffffff'],
                ['id' => 6, 'nombre' => 'Proceso de Pago', 'color' => '#f97316', 'orden' => 6, 'color_texto' => '#ffffff'],
                ['id' => 7, 'nombre' => 'Pagado', 'color' => '#22c55e', 'orden' => 7, 'color_texto' => '#ffffff'],
                ['id' => 8, 'nombre' => 'Cancelado', 'color' => '#ef4444', 'orden' => 8, 'color_texto' => '#ffffff'],
                ['id' => 9, 'nombre' => 'Nota de Crédito', 'color' => '#64748b', 'orden' => 9, 'color_texto' => '#ffffff'],
                ['id' => 10, 'nombre' => 'Cerrado', 'color' => '#0f172a', 'orden' => 10, 'color_texto' => '#ffffff'],
            ]);
        }

        // 4. Historial Estados OT
        if (!Schema::hasTable('orden_trabajo_estados')) {
            Schema::create('orden_trabajo_estados', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('orden_trabajo_id');
                $table->unsignedBigInteger('estado_anterior')->nullable();
                $table->unsignedBigInteger('estado_nuevo');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }

        // 5. Catálogo Aspectos Informe
        if (!Schema::hasTable('informe_aspectos_catalogo')) {
            Schema::create('informe_aspectos_catalogo', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->boolean('aplica_inicial')->default(true);
                $table->boolean('aplica_final')->default(true);
                $table->boolean('permite_imagenes')->default(false);
                $table->integer('orden')->default(1);
                $table->timestamps();
            });

            DB::table('informe_aspectos_catalogo')->insert([
                ['id' => 1, 'nombre' => 'ANTECEDENTES Y ESTADO INICIAL', 'aplica_inicial' => 1, 'aplica_final' => 1, 'permite_imagenes' => 0, 'orden' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 2, 'nombre' => 'ACTIVIDADES Y TRABAJOS EJECUTADOS', 'aplica_inicial' => 1, 'aplica_final' => 1, 'permite_imagenes' => 0, 'orden' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 3, 'nombre' => 'REGISTRO FOTOGRÁFICO DE EVIDENCIA', 'aplica_inicial' => 1, 'aplica_final' => 1, 'permite_imagenes' => 1, 'orden' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['id' => 4, 'nombre' => 'CONCLUSIONES Y RECOMENDACIONES TÉCNICAS', 'aplica_inicial' => 1, 'aplica_final' => 1, 'permite_imagenes' => 0, 'orden' => 4, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // 6. Informes
        if (!Schema::hasTable('informes')) {
            Schema::create('informes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('orden_trabajo_id')->nullable();
                $table->string('informe_tipo')->default('tecnico');
                $table->date('fecha_creacion')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('tecnico_id')->nullable();
                $table->unsignedBigInteger('sede_id')->nullable();
                $table->timestamps();
            });
        }

        // 7. Informe Aspectos
        if (!Schema::hasTable('informe_aspectos')) {
            Schema::create('informe_aspectos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('informe_id');
                $table->unsignedBigInteger('aspecto_id');
                $table->timestamps();
            });
        }

        // 8. Informe Aspectos Items
        if (!Schema::hasTable('informe_aspectos_items')) {
            Schema::create('informe_aspectos_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('informe_aspecto_id');
                $table->text('contenido')->nullable();
                $table->integer('orden')->default(1);
                $table->timestamps();
            });
        }

        // 9. Informe Aspecto Imágenes
        if (!Schema::hasTable('informe_aspecto_imagenes')) {
            Schema::create('informe_aspecto_imagenes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('informe_aspecto_id');
                $table->string('ruta_imagen');
                $table->string('grupo_id')->nullable();
                $table->string('grupo_descripcion')->nullable();
                $table->integer('orden_grupo')->default(1);
                $table->integer('orden')->default(1);
                $table->timestamps();
            });
        }

        // 10. Actualizar columnas de ordenes_trabajo
        if (Schema::hasTable('ordenes_trabajo')) {
            Schema::table('ordenes_trabajo', function (Blueprint $table) {
                if (!Schema::hasColumn('ordenes_trabajo', 'sede_id')) {
                    $table->unsignedBigInteger('sede_id')->nullable();
                }
                if (!Schema::hasColumn('ordenes_trabajo', 'responsable_id')) {
                    $table->unsignedBigInteger('responsable_id')->nullable();
                }
                if (!Schema::hasColumn('ordenes_trabajo', 'fecha_registro')) {
                    $table->date('fecha_registro')->nullable();
                }
                if (!Schema::hasColumn('ordenes_trabajo', 'fecha_atencion')) {
                    $table->date('fecha_atencion')->nullable();
                }
                if (!Schema::hasColumn('ordenes_trabajo', 'motivo')) {
                    $table->string('motivo')->nullable();
                }
                if (!Schema::hasColumn('ordenes_trabajo', 'descripcion')) {
                    $table->text('descripcion')->nullable();
                }
                if (!Schema::hasColumn('ordenes_trabajo', 'archivo_factura')) {
                    $table->string('archivo_factura')->nullable();
                }
                if (!Schema::hasColumn('ordenes_trabajo', 'archivo_sustento')) {
                    $table->string('archivo_sustento')->nullable();
                }
                if (!Schema::hasColumn('ordenes_trabajo', 'archivo_pdf')) {
                    $table->string('archivo_pdf')->nullable();
                }
                if (!Schema::hasColumn('ordenes_trabajo', 'captura_info')) {
                    $table->string('captura_info')->nullable();
                }
                if (!Schema::hasColumn('ordenes_trabajo', 'proceso_pago')) {
                    $table->string('proceso_pago')->nullable();
                }
            });
        }

        // 11. Tablas auxiliares comerciales
        if (!Schema::hasTable('servicios')) {
            Schema::create('servicios', function (Blueprint $table) {
                $table->id();
                $table->string('descripcion');
                $table->string('unidad_codigo')->default('SRV');
                $table->decimal('precio', 10, 2)->default(0);
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('transporte')) {
            Schema::create('transporte', function (Blueprint $table) {
                $table->id();
                $table->string('descripcion')->default('Transporte de Herramientas');
                $table->string('unidad_codigo')->default('GLB');
                $table->decimal('precio', 10, 2)->default(0);
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tpersonal')) {
            Schema::create('tpersonal', function (Blueprint $table) {
                $table->id();
                $table->string('descripcion')->default('Transporte de Personal');
                $table->string('unidad_codigo')->default('GLB');
                $table->decimal('precio', 10, 2)->default(0);
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('condiciones')) {
            Schema::create('condiciones', function (Blueprint $table) {
                $table->id();
                $table->text('descripcion');
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('bancos')) {
            Schema::create('bancos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('numero_cuenta')->nullable();
                $table->string('cci')->nullable();
                $table->string('moneda')->default('PEN');
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cotizacion_productos')) {
            Schema::create('cotizacion_productos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cotizacion_id');
                $table->unsignedBigInteger('producto_id')->nullable();
                $table->decimal('cantidad', 10, 2)->default(1);
                $table->decimal('precio_unitario', 10, 2)->default(0);
                $table->decimal('subtotal', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cotizacion_servicios')) {
            Schema::create('cotizacion_servicios', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cotizacion_id');
                $table->unsignedBigInteger('servicio_id')->nullable();
                $table->decimal('cantidad', 10, 2)->default(1);
                $table->decimal('precio_unitario', 10, 2)->default(0);
                $table->decimal('subtotal', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cotizacion_transportes')) {
            Schema::create('cotizacion_transportes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cotizacion_id');
                $table->unsignedBigInteger('transporte_id')->nullable();
                $table->decimal('cantidad', 10, 2)->default(1);
                $table->decimal('precio_unitario', 10, 2)->default(0);
                $table->decimal('subtotal', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cotizacion_tpersonal')) {
            Schema::create('cotizacion_tpersonal', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cotizacion_id');
                $table->unsignedBigInteger('tpersonal_id')->nullable();
                $table->decimal('cantidad', 10, 2)->default(1);
                $table->decimal('precio_unitario', 10, 2)->default(0);
                $table->decimal('subtotal', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cotizacion_condiciones')) {
            Schema::create('cotizacion_condiciones', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cotizacion_id');
                $table->unsignedBigInteger('condicion_id')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Safe down
    }
};
