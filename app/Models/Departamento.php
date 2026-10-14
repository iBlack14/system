<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    //
       protected $table = 'ubigeo_peru_departments';
       
    protected $keyType = 'string';
    public $incrementing = false;
        protected $fillable = ['id', 'name'];
    public $timestamps = false;
}
