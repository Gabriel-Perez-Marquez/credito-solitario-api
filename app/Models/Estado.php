<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $fillable = [
        'nombre',
    ];

    /**
     * Relación con pedidos
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }
}
