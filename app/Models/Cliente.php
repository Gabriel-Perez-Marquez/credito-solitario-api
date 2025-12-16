<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nombre',
        'apellidos',
        'telefono',
        'email',
        'direccion_id',
    ];

    /**
     * Relación con dirección
     */
    public function direccion()
    {
        return $this->belongsTo(Direccion::class);
    }

    /**
     * Relación con pedidos
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
