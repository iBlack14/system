<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TPersonal extends Model
{
    //
     //
        protected $table = 'tpersonal';
        protected $fillable = [
       //'codigo',
        'department_id',
        'province_id',
        'districts_id',
        'unidad_codigo',
        'precio'
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

    public function unidad()
    {
        return $this->belongsTo(Unidad::class, 'unidad_codigo', 'codigo');
    }
}
