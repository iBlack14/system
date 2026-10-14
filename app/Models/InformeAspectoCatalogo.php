<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformeAspectoCatalogo extends Model
{
    protected $table = 'informe_aspectos_catalogo';

    protected $fillable = [
        'nombre',
        'aplica_inicial',
        'aplica_final',
        'permite_imagenes',
        'orden'
    ];


    public function aspectos()
    {
        return $this->hasMany(
            InformeAspecto::class,
            'aspecto_id'
        );
    }
}
