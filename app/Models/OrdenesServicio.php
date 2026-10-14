<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenesServicio extends Model
{
    //
     protected $table = 'ordenes_servicio';
        protected $fillable = [
       //'codigo',
       'ot_codigo',
       'codigo_orden_servicio',
       'user_id',
       'cliente_id',
       'fecha_creacion',
       'fecha_entrega',
       'forma_pago',
       'lugar_entrega',
       'descripcion',
       'penalidad',
       'monto',
       'estado',
       'observaciones',
       'dias',
       'archivo_pdf',
    ];
     public $timestamps = true;

        // 🔗 Relación con usuario que generó la orden
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
     // 🔗 Relación con cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    public function ordenTrabajo()
    {
    return $this->belongsTo(OrdenTrabajo::class,'ot_codigo','id');
    }
}
