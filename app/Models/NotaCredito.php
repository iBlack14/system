<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaCredito extends Model
{
    //

        protected $table = 'notas_credito';
        protected $fillable = [
       'serie',
       'correlativo',
       'archivo_nota',
       'valida_nota',
       'fecha_registro',
       'detalles_nota',
       'monto_nota'
    ];
     public $timestamps = false;

     public function ordenesTrabajo()
    {
        return $this->hasMany(OrdenTrabajo::class, 'nota_credito_id','id');
    }
}
