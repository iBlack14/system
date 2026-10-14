<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformeAspecto extends Model
{

    protected $table = 'informe_aspectos';
    protected $fillable = [
        'informe_id',
        'aspecto_id',
        //'contenido'
    ];

    public function informe()
    {
        return $this->belongsTo(Informes::class, 'informe_id');
    }

    public function aspectoCatalogo()
    {
        return $this->belongsTo(InformeAspectoCatalogo::class,'aspecto_id');
    }

    public function items()
    {
        return $this->hasMany(InformeAspectoItem::class, 'informe_aspecto_id');
    }

    /* public function imagenes()
    {
        return $this->hasMany(InformeAspectoImagen::class, 'informe_aspecto_id');
    } */

    public function imagenes()
    {
        return $this->hasMany(InformeAspectoImagen::class)->orderBy('orden');
    }
}
