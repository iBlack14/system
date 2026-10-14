<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManoObra extends Model
{
    //
        protected $table = 'mano_obra';
        protected $fillable = [
       'descripcion',
       'precio'
    ];
     public $timestamps = false;
}
