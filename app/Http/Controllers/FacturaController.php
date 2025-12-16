<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $facturas = Factura::with(['pedido', 'cliente'])->get();
        return response()->json($facturas);
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
            'pedido_id' => 'required|exists:pedidos,id|unique:facturas,pedido_id',
            'cliente_id' => 'required|exists:clientes,id',
            'fechaCreacion' => 'required|date',
        ]);

        $factura = Factura::create($validated);
        $factura->load(['pedido', 'cliente']);

        return response()->json($factura, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Factura $factura)
    {
        $factura->load(['pedido.lineasVenta.producto', 'cliente']);
        return response()->json($factura);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Factura $factura)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Factura $factura)
    {
        $validated = $request->validate([
            'pedido_id' => 'sometimes|exists:pedidos,id|unique:facturas,pedido_id,' . $factura->id,
            'cliente_id' => 'sometimes|exists:clientes,id',
            'fechaCreacion' => 'sometimes|date',
        ]);

        $factura->update($validated);
        $factura->load(['pedido', 'cliente']);

        return response()->json($factura);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Factura $factura)
    {
        $factura->delete();
        return response()->json(['message' => 'Factura eliminada correctamente'], 200);
    }
}
