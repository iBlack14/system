<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ventas;
use App\Models\Servicios;
 use HasFactory;

class VentasServicio extends Model
{
    //
    protected $table = 'ventas_servicio';

    protected $fillable = [
        'ventas_id',
        'servicio_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        ];
    public function ventas()
    {
        return $this->belongsTo(Ventas::class,'ventas_id');
    }

    // 🔗 Relación con Servicio
    public function servicio()
    {
        return $this->belongsTo(Servicios::class,'servicio_id');
    }


}
