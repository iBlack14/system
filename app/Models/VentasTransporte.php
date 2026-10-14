<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ventas;
use App\Models\Transporte;
use HasFactory;

class VentasTransporte extends Model
{
    //
    protected $table = 'ventas_transporte';

    protected $fillable = [
        'ventas_id',
        'transporte_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

     public function ventas()
    {
        return $this->belongsTo(Ventas::class,'ventas_id');
    }

    // 🔗 Relación con Transporte
    public function transporte()
    {
        return $this->belongsTo(Transporte::class,'transporte_id');
    }

}
