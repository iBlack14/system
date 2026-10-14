<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenTrabajoDetalle extends Model
{
    //
    protected $table = 'orden_trabajo_detalles';

    protected $fillable = [
        'orden_trabajo_id',
        'descripcion',
        ];
        public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'orden_trabajo_id');
    }

    // 🔗 Relación opcional con producto
    //public function producto()
    //{
    //    return $this->belongsTo(Producto::class, 'producto_id');
    //}
}
