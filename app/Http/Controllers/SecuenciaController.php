<?php

namespace App\Http\Controllers;

use App\Models\Secuencia;
use Illuminate\Http\Request;

class SecuenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $secuencias = Secuencia::all();
        return response()->json($secuencias);
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
            'nombre' => 'required|string|max:255|unique:secuencias,nombre',
            'prefijo' => 'nullable|string|max:10',
            'valor_actual' => 'sometimes|integer|min:0',
            'incremento' => 'sometimes|integer|min:1',
        ]);

        // Valores por defecto
        $validated['valor_actual'] = $validated['valor_actual'] ?? 0;
        $validated['incremento'] = $validated['incremento'] ?? 1;

        $secuencia = Secuencia::create($validated);

        return response()->json($secuencia, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Secuencia $secuencia)
    {
        return response()->json($secuencia);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Secuencia $secuencia)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Secuencia $secuencia)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255|unique:secuencias,nombre,' . $secuencia->id,
            'prefijo' => 'nullable|string|max:10',
            'valor_actual' => 'sometimes|integer|min:0',
            'incremento' => 'sometimes|integer|min:1',
        ]);

        $secuencia->update($validated);

        return response()->json($secuencia);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Secuencia $secuencia)
    {
        $secuencia->delete();
        return response()->json(['message' => 'Secuencia eliminada correctamente'], 200);
    }

    /**
     * Obtiene el siguiente número de la secuencia
     */
    public function siguiente(Secuencia $secuencia)
    {
        $siguiente = $secuencia->siguiente();
        return response()->json([
            'secuencia' => $secuencia->nombre,
            'siguiente' => $siguiente,
        ]);
    }

    /**
     * Resetea una secuencia
     */
    public function resetear(Request $request, Secuencia $secuencia)
    {
        $validated = $request->validate([
            'valor' => 'sometimes|integer|min:0',
        ]);

        $secuencia->resetear($validated['valor'] ?? 0);

        return response()->json([
            'message' => 'Secuencia reseteada correctamente',
            'secuencia' => $secuencia,
        ]);
    }
}
