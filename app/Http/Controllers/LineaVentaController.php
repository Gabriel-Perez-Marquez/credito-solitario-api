<?php

namespace App\Http\Controllers;

use App\Models\LineaVenta;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LineaVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LineaVenta::with(['pedido', 'producto']);
       
        if ($request->has('pedido_id')) {
            $query->where('pedido_id', $request->pedido_id);
        }

        return response()->json($query->paginate(20));
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

        try {
            return DB::transaction(function () use ($validated) {
                $producto = Producto::lockForUpdate()->find($validated['producto_id']);


                if ($producto->stock < $validated['cantidad']) {
                    throw new \Exception("Stock insuficiente. Disponible: {$producto->stock}");
                }


                $validated['precioTotal'] = $validated['cantidad'] * $validated['precioUnidad'];


                $lineaVenta = LineaVenta::create($validated);

                $producto->decrement('stock', $validated['cantidad']);

                $lineaVenta->load(['pedido', 'producto']);

                return response()->json($lineaVenta, 201);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, LineaVenta $lineaVenta)
    {
        $validated = $request->validate([
            'cantidad' => 'sometimes|integer|min:1',
            'precioUnidad' => 'sometimes|numeric|min:0',
        ]);

        $cantidad = $validated['cantidad'] ?? $lineaVenta->cantidad;
        $precioUnidad = $validated['precioUnidad'] ?? $lineaVenta->precioUnidad;
        
        $validated['precioTotal'] = $cantidad * $precioUnidad;

        $lineaVenta->update($validated);
        
        return response()->json($lineaVenta);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LineaVenta $lineaVenta)
    {
        $lineaVenta->delete();
        return response()->json(['message' => 'Línea eliminada correctamente'], 200);
    }
}