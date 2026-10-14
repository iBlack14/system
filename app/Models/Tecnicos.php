<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tecnicos extends Model
{
    //
    protected $table = 'tecnicos';
    //protected $primaryKey = 'codigo';
    //public $incrementing = false;
    //protected $keyType = 'string';
    protected $fillable = [
       //'codigo',
       'dni',
       'nombre',
       'department_id',
       'province_id',
       'districts_id',
       'email',
       'telefono',
       'profesion',
       'puesto',
       'activo',
    ];

     public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'department_id', 'id');
    }

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'province_id','id');
    }

     public function distrito()
    {
        return $this->belongsTo(Distrito::class, 'districts_id','id');
    }
}
