<?php

namespace App\Models;
use App\Models\Producto;
use App\Models\Transporte;
use App\Models\OrdenTrabajo;
use App\Models\Cliente;
use App\Models\ComprobanteTipo;
use App\Models\VentasTransporte;
use App\Models\VentasServicio;
use App\Models\VentasProducto;
use App\Models\VentasTPersonal;
use App\Models\VentasCondiciones;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ventas extends Model
{
    //
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'id',
        'cliente_id',
        'comprobante_tipo_codigo',
        'user_id',
        'ot_codigo',
        'serie',
        'correlativo',
        //'categoria',
        'forma_pago',
        'fecha',
        'cotizacion_id',
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
        return $this->hasMany(VentasServicio::class,'ventas_id');
    }
     public function tpersonal()
    {
        return $this->hasMany(VentasTPersonal::class,'ventas_id');
    }
     public function transportes()
    {
        return $this->hasMany(VentasTransporte::class,'ventas_id');
    }
    public function productos()
    {
        return $this->hasMany(VentasProducto::class, 'ventas_id');
    }

    public function distritos()
    {
        return $this->hasMany(Distrito::class, 'transporte_id');
    }

     public function condiciones()
    {
        return $this->hasMany(VentasCondiciones::class, 'ventas_id');
    }
     public function comprobanteTipo()
    {
        return $this->belongsTo(ComprobanteTipo::class, 'comprobante_tipo_codigo', 'codigo');
    }
    
}
