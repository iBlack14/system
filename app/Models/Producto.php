<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Producto extends Model
{
    protected $fillable = [
        'unidad_codigo',
        'afectacion_tipo_codigo',
        'codigo',
        'nombre',
        'descripcion',
        'imagen',
        'costo_unitario',
        'utilidad',
        'stock',
        'precio_venta',
        'almacen',
        'ubicacion_columna',
        'ubicacion_fila',
        'porcentaje5',
        'porcentaje10',
        'porcentaje50',
        'porcentaje100'
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'unidad_codigo', 'codigo');
    }

    public function afectacionTipo()
    {
        return $this->belongsTo(AfectacionTipo::class, 'afectacion_tipo_codigo', 'codigo');
    }

    public static function updateStock(bool $increase, array $detalles) {
        DB::transaction(function () use ($increase, $detalles) {
            foreach ($detalles as $detalle) {
                $producto = self::findOrFail($detalle['producto_id']);
                $cantidad = floatval($detalle['cantidad']);
                if ($increase) {
                    $producto->stock += $cantidad;
                } else {
                    if ($producto->stock < $cantidad) {
                        throw new \Exception("El producto {$producto->nombre} no tiene suficiente stock.");
                    }
                    $producto->stock -= $cantidad;
                }
                $producto->save();
            }
        });
    }

    public function salidasAlmacen()
    {
        return $this->hasMany(
            SalidaAlmacenProducto::class,
            'producto_id'
        );
    }

    public function wooCommerceLink()
    {
        return $this->hasOne(WooCommerceProductLink::class, 'producto_id');
    }

}
