<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cotizaciones;
use App\Models\ManoObra;
 use HasFactory;

class CotizacionManoObra extends Model
{
    //
   
    protected $table= 'cotizacion_mano_obra';

    protected $fillable = ['cotizacion_id', 'mano_obra_id', 'cantidad', 'precio_unitario','subtotal'];
    
    public function cotizacion()
    {
        return $this->belongsTo(Cotizaciones::class,'cotizacion_id');
    }

    public function manoObra()
    {
        return $this->belongsTo(ManoObra::class,'mano_obra_id');
    }
}
