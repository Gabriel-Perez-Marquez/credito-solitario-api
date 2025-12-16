<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use Illuminate\Http\Request;

class DireccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $direcciones = Direccion::all();
        return response()->json($direcciones);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Este método no es necesario para una API REST
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'municipio' => 'required|string|max:255',
            'calle' => 'required|string|max:255',
            'numCasa' => 'required|string|max:255',
            'provincia' => 'required|string|max:255',
        ]);

        $direccion = Direccion::create($validated);

        return response()->json($direccion, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Direccion $direccion)
    {
        return response()->json($direccion);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Direccion $direccion)
    {
        // Este método no es necesario para una API REST
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Direccion $direccion)
    {
        $validated = $request->validate([
            'municipio' => 'sometimes|string|max:255',
            'calle' => 'sometimes|string|max:255',
            'numCasa' => 'sometimes|string|max:255',
            'provincia' => 'sometimes|string|max:255',
        ]);

        $direccion->update($validated);

        return response()->json($direccion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Direccion $direccion)
    {
        $direccion->delete();
        return response()->json(['message' => 'Dirección eliminada correctamente'], 200);
    }
}
