<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    //

    protected $table = 'pagos';

    protected $fillable = [
        'ot_codigo',
        'banco_id',
        'monto_pagado',
        'monto_detraccion',
        'fecha_pago',
        'num_operacion',
        'voucher_pago'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bancos()
    {
        return $this->belongsTo(Bancos::class, 'banco_id');
    }

    public function ordenTrabajo()
    {
    return $this->belongsTo(OrdenTrabajo::class,'ot_codigo','id');
    }
}
