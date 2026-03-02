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
        'dni',          
        'saldo',        
        'activo',       
        'direccion_id',
        'user_id'
    ];


    /**
     * Los casts convierten los datos automáticamente al tipo correcto
     */
    protected $casts = [
        'activo' => 'boolean',     
        'saldo' => 'decimal:2',   
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
