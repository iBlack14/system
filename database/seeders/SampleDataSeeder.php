<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Sedes;
use App\Models\Responsables;
use App\Models\Tecnicos;
use App\Models\OrdenTrabajo;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $c = Cliente::firstOrCreate(
            ['numero_documento' => '20100035121'],
            [
                'razon_social' => 'CEMENTOS PACASMAYO S.A.A.',
                'direccion' => 'Calle La Colonia 180, Pacasmayo',
                'documento_tipo_codigo' => '06'
            ]
        );

        $s = Sedes::firstOrCreate(
            ['nombre' => 'Planta Central - Molino 3', 'cliente_id' => $c->id],
            ['direccion' => 'Pacasmayo', 'activo' => 1]
        );

        $r = Responsables::firstOrCreate(
            ['nombre' => 'Ing. Carlos Morales', 'cliente_id' => $c->id],
            ['cargo' => 'Jefe Mantenimiento', 'email' => 'cmorales@pacasmayo.com.pe', 'activo' => 1]
        );

        $t = Tecnicos::firstOrCreate(
            ['nombre' => 'Ing. Miguel Torres'],
            ['telefono' => '987654321', 'email' => 'mtorres@acotec.pe', 'activo' => 1]
        );

        OrdenTrabajo::firstOrCreate(
            ['codigo_ot_cliente' => 'OT-2026-0842'],
            [
                'cliente_id' => $c->id,
                'sede_id' => $s->id,
                'responsable_id' => $r->id,
                'tecnico_id' => $t->id,
                'fecha_registro' => now(),
                'fecha_atencion' => now(),
                'motivo' => 'Mantenimiento Preventivo PLC',
                'descripcion' => 'Revisión y mantenimiento preventivo de tableros de control',
                'estado' => 1,
                'user_id' => 1,
                'fecha' => now()
            ]
        );
    }
}
