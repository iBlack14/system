<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformeAspectoItem extends Model
{
    use HasFactory;

    protected $table = 'informe_aspectos_items';

    protected $fillable = [
        'informe_aspecto_id',
        'contenido',
        'orden'
    ];

    /**
     * Relación: el ítem pertenece a un aspecto
     */
    public function aspecto()
    {
        return $this->belongsTo(InformeAspecto::class,'aspecto_id');
    }
}
