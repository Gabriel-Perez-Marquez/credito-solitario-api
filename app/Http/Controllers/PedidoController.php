<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pedidos = Pedido::with(['cliente', 'estado', 'lineasVenta.producto'])->get();
        return response()->json($pedidos);
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
            'nombre' => 'nullable|string|max:255',
            'tipo' => 'nullable|in:venta,reposicion',
            'cliente_id' => 'nullable|exists:clientes,id',
            'estado_id' => 'required|exists:estados,id',
            'direccionEntrega' => 'required|string|max:255',
            'fechaPedido' => 'required|date',
            'fechaEntrega' => 'nullable|date|after_or_equal:fechaPedido',
        ]);

        $validated['tipo'] = $validated['tipo'] ?? 'venta';
        if ($validated['tipo'] === 'venta' && empty($validated['cliente_id'])) {
            return response()->json([
                'message' => 'El cliente_id es obligatorio para pedidos de venta',
            ], 422);
        }

        $pedido = Pedido::create($validated);
        $pedido->load(['cliente', 'estado']);

        return response()->json($pedido, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pedido $pedido)
    {
        $pedido->load(['cliente', 'estado', 'lineasVenta.producto', 'factura']);
        return response()->json($pedido);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pedido $pedido)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pedido $pedido)
    {
        $validated = $request->validate([
            'nombre' => 'nullable|string|max:255',
            'tipo' => 'sometimes|in:venta,reposicion',
            'cliente_id' => 'sometimes|nullable|exists:clientes,id',
            'estado_id' => 'sometimes|exists:estados,id',
            'direccionEntrega' => 'sometimes|string|max:255',
            'fechaPedido' => 'sometimes|date',
            'fechaEntrega' => 'nullable|date|after_or_equal:fechaPedido',
        ]);

        $pedido->update($validated);
        $pedido->load(['cliente', 'estado']);

        return response()->json($pedido);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pedido $pedido)
    {
        $pedido->delete();
        return response()->json(['message' => 'Pedido eliminado correctamente'], 200);
    }
}
