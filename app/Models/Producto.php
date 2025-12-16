<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'precio',
        'activo',
        'descuento',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'descuento' => 'decimal:2',
        'activo' => 'boolean',
    ];

    /**
     * Relación con categoría
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Relación con líneas de venta
     */
    public function lineasVenta()
    {
        return $this->hasMany(LineaVenta::class);
    }

    /**
     * Relación con imágenes
     */
    public function imagenes()
    {
        return $this->hasMany(ImagenProducto::class);
    }

    /**
     * Calcula el precio final después de aplicar descuento
     */
    public function getPrecioFinalAttribute()
    {
        return $this->precio - ($this->precio * $this->descuento / 100);
    }
}
