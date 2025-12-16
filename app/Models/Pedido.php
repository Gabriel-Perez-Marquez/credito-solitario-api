<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'nombre',
        'cliente_id',
        'estado_id',
        'direccionEntrega',
        'fechaPedido',
        'fechaEntrega',
    ];

    protected $casts = [
        'fechaPedido' => 'datetime',
        'fechaEntrega' => 'datetime',
    ];

    /**
     * Relación con cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con estado
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    /**
     * Relación con líneas de venta
     */
    public function lineasVenta()
    {
        return $this->hasMany(LineaVenta::class);
    }

    /**
     * Relación con factura
     */
    public function factura()
    {
        return $this->hasOne(Factura::class);
    }
}
