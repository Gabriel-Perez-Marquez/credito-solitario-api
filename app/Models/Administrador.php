<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    protected $fillable = [
        'user_id', 
        'nombre',
        'apellidos',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
