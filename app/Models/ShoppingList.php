<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    protected $table = 'shopping_lists';

    protected $fillable = [
        'producto_id',
        'cantidad',
        'prioridad', 
    ];

    protected $casts = [
        'cantidad' => 'integer',
        
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}