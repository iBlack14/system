<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    //
       protected $table = 'ubigeo_peru_provinces';
       
    protected $keyType = 'string';
    public $incrementing = false;
       protected $fillable = ['id', 'name', 'department_id'];
       public $timestamps = false;

      public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id', 'name');
    }
    
}
