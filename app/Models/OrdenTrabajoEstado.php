<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenTrabajoEstado extends Model
{
    //

    protected $table = 'orden_trabajo_estados';

    public $timestamps = false;

    protected $fillable = [
        'orden_trabajo_id',
        'estado_anterior',
        'estado_nuevo',
        'user_id',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function ordenTrabajo()
    {
        return $this->belongsTo(OrdenTrabajo::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function estadoNuevo()
    {
        return $this->belongsTo(OrdenTrabajoEstadoCatalogo::class, 'estado_nuevo');
    }

    public function estadoAnterior()
    {
        return $this->belongsTo(
            OrdenTrabajoEstadoCatalogo::class,
            'estado_anterior'
        );
    }


}
