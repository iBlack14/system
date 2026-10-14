<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicios extends Model
{
    //
        protected $table = 'servicios';
        protected $fillable = [
       //'codigo',
       'unidad_codigo',
       'descripcion',
       'precio',
    ];
     public $timestamps = false;

     public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'unidad_codigo', 'codigo');
    }
}
