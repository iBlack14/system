<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Condiciones extends Model
{
    //

    protected $table = 'condiciones';
        protected $fillable = [
       //'codigo',
       'descripcion',
    ];
     public $timestamps = false;
}
