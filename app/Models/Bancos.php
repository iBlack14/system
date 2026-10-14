<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bancos extends Model
{
    //

     protected $table = 'bancos';
        protected $fillable = [
       'descripcion'
    ];
     public $timestamps = false;
}
