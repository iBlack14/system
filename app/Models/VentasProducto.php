<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ventas;
use App\Models\Producto;
use HasFactory;

class VentasProducto extends Model
{
    //
    protected $table = 'ventas_productos';

    protected $fillable = [
        'ventas_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        ];

      public function ventas()
    {
        return $this->belongsTo(Ventas::class,'ventas_id');
    }

    // 🔗 Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class,'producto_id');
    }
}
