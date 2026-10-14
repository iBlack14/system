<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cotizaciones;
use App\Models\Servicios;
 use HasFactory;

class CotizacionServicio extends Model
{
    //
    protected $table = 'cotizacion_servicio';

    protected $fillable = [
        'cotizacion_id',
        'servicio_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        ];
    public function cotizacion()
    {
        return $this->belongsTo(Cotizaciones::class,'cotizacion_id');
    }

    // 🔗 Relación con Servicio
    public function servicio()
    {
        return $this->belongsTo(Servicios::class,'servicio_id');
    }


}
