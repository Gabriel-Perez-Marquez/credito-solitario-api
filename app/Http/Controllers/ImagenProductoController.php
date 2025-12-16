<?php

namespace App\Http\Controllers;

use App\Models\ImagenProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImagenProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $imagenes = ImagenProducto::with('producto')->get();
        return response()->json($imagenes);
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
            'producto_id' => 'required|exists:productos,id',
            'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'alt' => 'nullable|string|max:255',
            'es_principal' => 'sometimes|boolean',
        ]);

        // Subir imagen
        $path = $request->file('imagen')->store('productos', 'public');

        $imagenProducto = ImagenProducto::create([
            'producto_id' => $validated['producto_id'],
            'url' => $path,
            'alt' => $validated['alt'] ?? null,
            'es_principal' => $validated['es_principal'] ?? false,
        ]);

        $imagenProducto->load('producto');

        return response()->json($imagenProducto, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ImagenProducto $imagenProducto)
    {
        $imagenProducto->load('producto');
        return response()->json($imagenProducto);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ImagenProducto $imagenProducto)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ImagenProducto $imagenProducto)
    {
        $validated = $request->validate([
            'producto_id' => 'sometimes|exists:productos,id',
            'imagen' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'alt' => 'nullable|string|max:255',
            'es_principal' => 'sometimes|boolean',
        ]);

        // Si se sube nueva imagen, eliminar la anterior
        if ($request->hasFile('imagen')) {
            Storage::disk('public')->delete($imagenProducto->url);
            $validated['url'] = $request->file('imagen')->store('productos', 'public');
        }

        $imagenProducto->update($validated);
        $imagenProducto->load('producto');

        return response()->json($imagenProducto);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ImagenProducto $imagenProducto)
    {
        // Eliminar archivo de imagen
        Storage::disk('public')->delete($imagenProducto->url);
        
        $imagenProducto->delete();
        return response()->json(['message' => 'Imagen eliminada correctamente'], 200);
    }
}
