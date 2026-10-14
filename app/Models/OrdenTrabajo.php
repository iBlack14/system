<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenTrabajo extends Model
{
    //
    protected $table = 'ordenes_trabajo';

    protected $fillable = [
        'codigo_ot_cliente',
        'cliente_id',
        'tecnico_id',
        //'user_id',
        'sede_id',
        'responsable_id',
        'fecha_registro',
        'fecha_atencion',
        'motivo',
        'descripcion',
        'archivo_factura',
        'archivo_sustento',
        'archivo_pdf',
        'captura_info',
        'proceso_pago',
        'estado',
        'observaciones',        
        
    ];
    public $timestamps = true;

    // 🔗 Relación con cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    // 🔗 Relación con técnico
    public function tecnico()
    {
        return $this->belongsTo(Tecnicos::class, 'tecnico_id');
    }

    // 🔗 Relación con usuario que generó la orden
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sedes()
    {
        return $this->belongsTo(Sedes::class, 'sede_id');
    }

    public function responsables()
    {
        return $this->belongsTo(Responsables::class, 'responsable_id');
    }

    public function historialEstados()
    {
        return $this->hasMany(OrdenTrabajoEstado::class, 'orden_trabajo_id');
    }
    
    public function estadoActual()
    {
        return $this->belongsTo(OrdenTrabajoEstadoCatalogo::class, 'estado');
    }

    public function estadoCatalogo()
    {
    return $this->belongsTo(OrdenTrabajoEstadoCatalogo::class,'estado','id');
    }

    public function cotizacion()
    {
    return $this->hasOne(Cotizaciones::class,'ot_codigo','id');
    }

    public function ordenServicio()
    {
    return $this->hasOne(OrdenesServicio::class,'ot_codigo','id');
    }

    public function getTiempoTranscurridoAttribute()
    {
        $ultimoCambio = $this->historialEstados()
            ->where('estado_nuevo', $this->estado)
            ->latest('created_at')
            ->first();

            if (!$ultimoCambio) {
                return '-';
            }

        return $ultimoCambio->created_at->diffForHumans();
    }
    public function informes()
    {
    return $this->hasMany(Informes::class, 'orden_trabajo_id');
    }


    public function ventas()
    {
    return $this->hasOne(Ventas::class, 'ot_codigo','id');
    }

    public function pagos()
    {
    return $this->hasOne(Pagos::class, 'ot_codigo','id');
    }

    public function comprobantetipo()
    {
    return $this->hasOne(ComprobanteTipo::class, 'ot_codigo','id');
    }

        public function getTiempoDesdeCreacionAttribute()
    {
        if (!$this->fecha_registro) {
            return '-';
        }

        return \Carbon\Carbon::parse($this->fecha_registro)->diffForHumans();
    }

        public function notaCredito()
    {
        return $this->belongsTo(NotaCredito::class, 'nota_credito_id');
    }
}
