<?php

namespace App\Http\Controllers;

use App\Models\LineaVenta;
use Illuminate\Http\Request;

class LineaVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lineasVenta = LineaVenta::with(['pedido', 'producto'])->get();
        return response()->json($lineasVenta);
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
            'pedido_id' => 'required|exists:pedidos,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'precioUnidad' => 'required|numeric|min:0',
        ]);

        // Calcular precio total
        $validated['precioTotal'] = $validated['cantidad'] * $validated['precioUnidad'];

        $lineaVenta = LineaVenta::create($validated);
        $lineaVenta->load(['pedido', 'producto']);

        return response()->json($lineaVenta, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(LineaVenta $lineaVenta)
    {
        $lineaVenta->load(['pedido', 'producto']);
        return response()->json($lineaVenta);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LineaVenta $lineaVenta)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LineaVenta $lineaVenta)
    {
        $validated = $request->validate([
            'pedido_id' => 'sometimes|exists:pedidos,id',
            'producto_id' => 'sometimes|exists:productos,id',
            'cantidad' => 'sometimes|integer|min:1',
            'precioUnidad' => 'sometimes|numeric|min:0',
        ]);

        // Recalcular precio total si cambia cantidad o precio unitario
        if (isset($validated['cantidad']) || isset($validated['precioUnidad'])) {
            $cantidad = $validated['cantidad'] ?? $lineaVenta->cantidad;
            $precioUnidad = $validated['precioUnidad'] ?? $lineaVenta->precioUnidad;
            $validated['precioTotal'] = $cantidad * $precioUnidad;
        }

        $lineaVenta->update($validated);
        $lineaVenta->load(['pedido', 'producto']);

        return response()->json($lineaVenta);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LineaVenta $lineaVenta)
    {
        $lineaVenta->delete();
        return response()->json(['message' => 'Línea de venta eliminada correctamente'], 200);
    }
}
