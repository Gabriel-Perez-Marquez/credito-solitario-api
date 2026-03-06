<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = Producto::with('categoria')->get();
        return response()->json($productos);
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
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'precio' => 'required|numeric|min:0',
            'activo' => 'sometimes|boolean',
            'descuento' => 'sometimes|numeric|min:0|max:100',
            'stock' => 'required|integer|min:0',     
            'urlImagen' => 'nullable|string|url',
        ]);

        $producto = Producto::create($validated);
        $producto->load('categoria');

        return response()->json($producto, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        $producto->load(['categoria']);
        return response()->json($producto);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        // Este método no es necesario para una API REST
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
         $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria_id' => 'required|exists:categorias,id',
            'precio' => 'required|numeric|min:0',
            'activo' => 'sometimes|boolean',
            'descuento' => 'sometimes|numeric|min:0|max:100',
            'stock' => 'required|integer|min:0',     
            'urlImagen' => 'nullable|string|url',
        ]);

        $producto->update($validated);
        $producto->load('categoria');

        return response()->json($producto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        $producto->delete();
        return response()->json(['message' => 'Producto eliminado correctamente'], 200);
    }
}
