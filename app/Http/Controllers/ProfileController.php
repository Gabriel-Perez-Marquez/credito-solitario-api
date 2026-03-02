<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        // Validamos los datos combinados
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'telefono' => 'nullable|string|max:255',
            'direccion' => 'nullable|string|max:255', 
        ]);

        // Actualizamos el Login (User)
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Actualizamos el Perfil (Cliente) y su Dirección
        if ($user->cliente) {
            $user->cliente->update([
                'telefono' => $validated['telefono'] ?? $user->cliente->telefono,
                'nombre' => $validated['name'],
            ]);

            if ($user->cliente->direccion) {
                $user->cliente->direccion->update([
                    'calle' => $validated['direccion'] ?? $user->cliente->direccion->calle,
                ]);
            }
        }

        // Devolvemos el usuario con todas sus relaciones cargadas
        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user' => $user->load('cliente.direccion')
        ], 200);
    }
}