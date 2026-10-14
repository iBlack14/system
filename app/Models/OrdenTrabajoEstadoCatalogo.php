<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenTrabajoEstadoCatalogo extends Model
{
    protected $table = 'orden_trabajo_estado_catalogo';

    protected $fillable = [
        'nombre',
        'color',
        'orden',
        'color_texto'
    ];

    public $timestamps = false;
}

