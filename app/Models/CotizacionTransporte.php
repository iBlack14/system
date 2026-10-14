<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cotizaciones;
use App\Models\Transporte;
use HasFactory;

class CotizacionTransporte extends Model
{
    //
    protected $table = 'cotizacion_transporte';

    protected $fillable = [
        'cotizacion_id',
        'transporte_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

     public function cotizacion()
    {
        return $this->belongsTo(Cotizaciones::class,'cotizacion_id');
    }

    // 🔗 Relación con Transporte
    public function transporte()
    {
        return $this->belongsTo(Transporte::class,'transporte_id');
    }

}
