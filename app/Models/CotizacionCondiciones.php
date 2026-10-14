<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cotizaciones;
use App\Models\Condiciones;
 use HasFactory;

class CotizacionCondiciones extends Model
{
    //

    protected $table = 'cotizacion_condiciones';

    protected $fillable = [
        'cotizacion_id',
        'condicion_id',
        ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizaciones::class,'cotizacion_id');
    }

    // 🔗 Relación con Condiciones
    public function condiciones()
    {
        return $this->belongsTo(Condiciones::class,'condicion_id');
    }
}
