<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cotizaciones;
use App\Models\TPersonal;
use HasFactory;

class CotizacionTPersonal extends Model
{
    //
    protected $table = 'cotizacion_tpersonal';

    protected $fillable = [
        'cotizacion_id',
        'tpersonal_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

     public function cotizacion()
    {
        return $this->belongsTo(Cotizaciones::class,'cotizacion_id');
    }

    // 🔗 Relación con Transporte Personal
    public function tpersonal()
    {
        return $this->belongsTo(TPersonal::class,'tpersonal_id');
    }

}
