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
            'direccion_id' => 'sometimes|exists:direccions,id',
        ]);

        $cliente->update($validated);
        $cliente->load('direccion');

        return response()->json($cliente);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return response()->json(['message' => 'Cliente eliminado correctamente'], 200);
    }
}
