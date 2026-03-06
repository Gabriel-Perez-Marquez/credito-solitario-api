<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Direccion;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        try {
            if (!$request->has('calle') && !$request->has('numCasa')) {
                return response()->json([
                    'message' => "¡Flutter sigue enviando la variable antigua! Asegúrate de guardar el archivo 'profile_service.dart' en tu editor de código y REINICIAR el emulador por completo (botón cuadrado rojo de Stop)."
                ], 400);
            }

            $user = $request->user();

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
                'telefono' => 'nullable|string|max:255',
                'calle' => 'nullable|string|max:255',      
                'numCasa' => 'nullable|string|max:255',     
                'municipio' => 'nullable|string|max:255',   
            ]);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $cliente = $user->cliente;

            if ($cliente) {
                $cliente->update([
                    'telefono' => $request->telefono ?? $cliente->telefono,
                    'nombre' => $request->name,
                ]);

                if ($cliente->direccion_id) {
                    DB::table('direccions')->where('id', $cliente->direccion_id)->update([
                        'calle' => $request->calle ?: 'Sin especificar',
                        'numCasa' => $request->numCasa ?: '0',
                        'municipio' => $request->municipio ?: 'Sin especificar',
                    ]);
                } else {
                    $nuevaDireccion = Direccion::create([
                        'calle' => $request->calle ?: 'Sin especificar',
                        'numCasa' => $request->numCasa ?: '0',
                        'municipio' => $request->municipio ?: 'Sin especificar',
                        'provincia' => 'Sin especificar',
                    ]);
                    $cliente->update(['direccion_id' => $nuevaDireccion->id]);
                }
            }
            
            return response()->json([
                'message' => 'Perfil actualizado correctamente',
                'user' => $user->fresh('cliente.direccion')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error del backend: ' . $e->getMessage()
            ], 500);
        }
    }
}