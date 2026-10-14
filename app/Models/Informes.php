<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Informes extends Model
{

protected $table = 'informes';
    protected $fillable = [
        'orden_trabajo_id',
        //'tipo',
        'fecha_creacion',
        'user_id',
    ];

   
    public function ordenTrabajo()
    {
    return $this->belongsTo(OrdenTrabajo::class,'orden_trabajo_id','id');
    }
   public function aspectos()
{
    return $this->hasMany(InformeAspecto::class, 'informe_id');
}

    public function tecnico()
    {
        return $this->belongsTo(Tecnicos::class, 'tecnico_id');
    }

     public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sedes()
    {
        return $this->belongsTo(Sedes::class, 'sede_id');
    }
}

