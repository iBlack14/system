<?php

namespace App\Services;

use App\Models\OrdenTrabajo;
use App\Models\OrdenTrabajoEstado;
use App\Models\OrdenTrabajoEstadoCatalogo;

class OrdenTrabajoEstadoService
{
    public static function cambiarEstado(
        OrdenTrabajo $orden,
        int $estadoNuevoId
    ): void {

        // Verifica que el estado exista en el catálogo
        OrdenTrabajoEstadoCatalogo::where('id', $estadoNuevoId)->firstOrFail();

        $estadoAnterior = $orden->estado;

        // Actualiza estado actual
        $orden->update([
            'estado' => $estadoNuevoId
        ]);

        // Guarda historial
        OrdenTrabajoEstado::create([
            'orden_trabajo_id' => $orden->id,
            'estado_anterior'  => $estadoAnterior,
            'estado_nuevo'     => $estadoNuevoId,
            'user_id'          => auth()->id(),
            'created_at'       => now()
        ]);
    }
}

