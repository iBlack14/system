<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ventas;
use App\Models\TPersonal;
use HasFactory;

class VentasTPersonal extends Model
{
    //
    protected $table = 'ventas_tpersonal';

    protected $fillable = [
        'ventas_id',
        'tpersonal_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

     public function ventas()
    {
        return $this->belongsTo(Ventas::class,'ventas_id');
    }

    // 🔗 Relación con Transporte Personal
    public function tpersonal()
    {
        return $this->belongsTo(TPersonal::class,'tpersonal_id');
    }

}
