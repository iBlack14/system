<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformeAspectoImagen extends Model
{

    protected $table = 'informe_aspecto_imagenes';
    protected $fillable = [
        'informe_aspecto_id',
        'ruta_imagen',
        'grupo_id',
        'grupo_descripcion',
        'orden_grupo',
        'orden'
    ];

    public function informeAspecto()
{
    return $this->belongsTo(InformeAspecto::class, 'informe_aspecto_id');
}
}
