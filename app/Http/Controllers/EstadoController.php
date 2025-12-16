<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $estados = Estado::all();
        return response()->json($estados);
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
            'nombre' => 'required|string|max:255|unique:estados,nombre',
        ]);

        $estado = Estado::create($validated);

        return response()->json($estado, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Estado $estado)
    {
        return response()->json($estado);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Estado $estado)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Estado $estado)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255|unique:estados,nombre,' . $estado->id,
        ]);

        $estado->update($validated);

        return response()->json($estado);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Estado $estado)
    {
        $estado->delete();
        return response()->json(['message' => 'Estado eliminado correctamente'], 200);
    }
}
