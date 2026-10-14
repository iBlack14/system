<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalidaAlmacen extends Model
{
    use HasFactory;

    protected $table = 'salidas_almacen';

    protected $fillable = [
        'cliente_id',
        'comprobante_tipo_codigo',
        'fecha_salida',
        'motivo',
        'observaciones',
        'estado',
        'total',
        'serie',
        'correlativo',
        'user_id',
    ];

    protected $casts = [
        'fecha_salida' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detalles()
    {
        return $this->hasMany(
            SalidaAlmacenProducto::class,
            'salida_id'
        );
    }
     public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}