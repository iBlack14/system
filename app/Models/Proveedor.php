<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        //'proveedor_id',
        'documento_tipo_codigo',
        'numero_documento',
        'razon_social',
        'direccion',
        'prov_contacto',
        'telefono',
        'email',
        'rubro'
    ];

    public function documentoTipo()
    {
        return $this->belongsTo(DocumentoTipo::class, 'documento_tipo_codigo', 'codigo');
    }
}
