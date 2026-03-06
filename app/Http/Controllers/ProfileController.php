<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Direccion;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'telefono' => 'nullable|string|max:255',
            'calle' => 'nullable|string|max:255',      
            'numCasa' => 'nullable|string|max:255',     
            'municipio' => 'nullable|string|max:255',   
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        $cliente = $user->cliente;

        if ($cliente) {
            $cliente->update([
                'telefono' => $request->telefono ?? $cliente->telefono,
                'nombre' => $request->name,
            ]);

            if ($cliente->direccion_id) {
                $direccionReal = Direccion::find($cliente->direccion_id);
                
                if ($direccionReal) {
                    $direccionReal->update([
                        'calle' => !empty($request->calle) ? $request->calle : $direccionReal->calle,
                        'numCasa' => !empty($request->numCasa) ? $request->numCasa : $direccionReal->numCasa,
                        'municipio' => !empty($request->municipio) ? $request->municipio : $direccionReal->municipio,
                    ]);
                }
            }
        }
        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user' => $user->fresh('cliente.direccion')
        ], 200);
    }
}