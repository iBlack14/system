<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Responsables extends Model
{
    //
    protected $table = 'responsables';

    protected $fillable = [
        'cliente_id',
        'nombre',
        'cargo',  
        'email',      
        'telefono',
        'activo',
    ];

     public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
