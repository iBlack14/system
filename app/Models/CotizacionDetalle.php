<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CotizacionDetalle extends Model
{
    //

        protected $table = 'cotizacion_detalles';
    public $timestamps = false;

    protected $fillable = [
        'cotizacion_id',
        'producto_id',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'total',
    ];
    
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
