<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    //
      protected $table = 'ubigeo_peru_districts';
      
    protected $keyType = 'string';
    public $incrementing = false;
      protected $fillable = ['id', 'name', 'province_id'];
      public $timestamps = false;

      public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id', 'name');
    }
    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'id', 'department_id','name');
    }
}
