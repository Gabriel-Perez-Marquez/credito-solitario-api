<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::with('direccion')->get();
        return response()->json($clientes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'telefono' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'password' => 'required|min:8',
            'dni' => 'required|string|max:20',
            'saldo' => 'nullable|numeric|min:0',
            'activo' => 'nullable|boolean',
            'direccion_id' => 'required|exists:direccions,id',
        ]);

        $validated['saldo'] = $validated['saldo'] ?? 0;
        $validated['activo'] = $validated['activo'] ?? true;

        $cliente = Cliente::create($validated);
        $cliente->load('direccion');

        return response()->json($cliente, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        $cliente->load('direccion', 'pedidos');
        return response()->json($cliente);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'apellidos' => 'sometimes|string|max:255',
            'telefono' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:clientes,email,' . $cliente->id,
            'dni' => 'sometimes|string|max:20',
            'saldo' => 'sometimes|numeric|min:0',
            'activo' => 'sometimes|boolean',
            'calle' => 'nullable|string|max:255',
            'numCasa' => 'nullable|string|max:255',
            'municipio' => 'nullable|string|max:255',
            'password' => 'nullable|min:8',
        ]);

        DB::transaction(function () use ($request, $cliente, $validated) {
        
        $cliente->update($request->only([
            'nombre', 'apellidos', 'telefono', 'email', 'dni', 'saldo', 'activo'
        ]));

        if ($cliente->user_id) {
            $userData = [];
            
            if ($request->has('nombre') || $request->has('apellidos')) {
                $userData['name'] = trim(($request->nombre ?? $cliente->nombre) . ' ' . ($request->apellidos ?? $cliente->apellidos));
            }
            if ($request->has('email')) {
                $userData['email'] = $request->email;
            }
            
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            if (!empty($userData)) {
                User::where('id', $cliente->user_id)->update($userData);
            }
        }

        if ($cliente->direccion_id) {
            $cliente->direccion()->update([
                'calle' => $request->calle ?? $cliente->direccion->calle,
                'numCasa' => $request->numCasa ?? $cliente->direccion->numCasa,
                'municipio' => $request->municipio ?? $cliente->direccion->municipio,
            ]);
        } else {
            $nuevaDireccion = Direccion::create([
                'calle' => $request->calle ?? 'Sin especificar',
                'numCasa' => $request->numCasa ?? '0',
                'municipio' => $request->municipio ?? 'Sin especificar',
            ]);
            $cliente->update(['direccion_id' => $nuevaDireccion->id]);
        }
    });

    $cliente->load(['direccion', 'user']);

    return response()->json($cliente);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);

        $userId = $cliente->user_id; 

        $cliente->delete();

        if ($userId) {
            \App\Models\User::where('id', $userId)->delete();
        }

        return response()->json(['message' => 'Cliente y cuenta de usuario eliminados correctamente']);
    }
}
