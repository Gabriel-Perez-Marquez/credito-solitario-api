<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Secuencia extends Model
{
    protected $fillable = [
        'nombre',
        'prefijo',
        'valor_actual',
        'incremento',
    ];

    protected $casts = [
        'valor_actual' => 'integer',
        'incremento' => 'integer',
    ];

    /**
     * Obtiene el siguiente valor de la secuencia
     */
    public function siguiente()
    {
        $this->valor_actual += $this->incremento;
        $this->save();
        
        return $this->prefijo . str_pad($this->valor_actual, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Resetea la secuencia a un valor específico
     */
    public function resetear($valor = 0)
    {
        $this->valor_actual = $valor;
        $this->save();
    }
}
