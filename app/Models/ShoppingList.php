<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    protected $fillable = ['producto_id', 'cantidad', 'prioridad'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
