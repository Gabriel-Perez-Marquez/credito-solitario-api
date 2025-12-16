<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineaVenta extends Model
{
    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'precioUnidad',
        'precioTotal',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precioUnidad' => 'decimal:2',
        'precioTotal' => 'decimal:2',
    ];

    /**
     * Relación con pedido
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Relación con producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
