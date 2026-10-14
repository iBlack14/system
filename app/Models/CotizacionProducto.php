<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cotizaciones;
use App\Models\Producto;
use HasFactory;

class CotizacionProducto extends Model
{
    //
    protected $table = 'cotizacion_productos';

    protected $fillable = [
        'cotizacion_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        ];

      public function cotizacion()
    {
        return $this->belongsTo(Cotizaciones::class,'cotizacion_id');
    }

    // 🔗 Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class,'producto_id');
    }
}
