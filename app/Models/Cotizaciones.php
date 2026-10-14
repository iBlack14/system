<?php

namespace App\Models;
use App\Models\Producto;
use App\Models\Transporte;
use App\Models\OrdenTrabajo;
use App\Models\Cliente;
use App\Models\CotizacionTransporte;
use App\Models\CotizacionServicio;
use App\Models\CotizacionProducto;
use App\Models\CotizacionTPersonal;
use App\Models\CotizacionCondiciones;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cotizaciones extends Model
{
    //
    use HasFactory;

    protected $table = 'cotizaciones';

    protected $fillable = [
        'id',
        'cliente_id',
        'user_id',
        'ot_codigo',
        //'categoria',
        'forma_pago',
        'fecha',
        //'validez',
        //'tiempo_entrega',
        //'garantia',
        'total',
        'estado',
        'observaciones',
        ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔹 Relación con el producto
   // public function producto()
    //{
      //  return $this->belongsTo(Producto::class, 'producto_id');
    //}
     public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    public function ordenTrabajo()
    {
    return $this->belongsTo(OrdenTrabajo::class,'ot_codigo','id');
    }

     public function servicios()
    {
        return $this->hasMany(CotizacionServicio::class,'cotizacion_id');
    }
     public function tpersonal()
    {
        return $this->hasMany(CotizacionTPersonal::class,'cotizacion_id');
    }
     public function transportes()
    {
        return $this->hasMany(CotizacionTransporte::class,'cotizacion_id');
    }
    public function productos()
    {
        return $this->hasMany(CotizacionProducto::class, 'cotizacion_id');
    }

    public function distritos()
    {
        return $this->hasMany(Distrito::class, 'transporte_id');
    }

     public function condiciones()
    {
        return $this->hasMany(CotizacionCondiciones::class, 'cotizacion_id');
    }

    // public function sedes()
    // {
    //     return $this->belongsTo(Sedes::class, 'sede_id');
    // }
    
}
