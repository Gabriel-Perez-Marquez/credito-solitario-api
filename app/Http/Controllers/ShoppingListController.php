<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use App\Models\LineaVenta;
use App\Models\Pedido;
use App\Models\ShoppingList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShoppingListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lista = ShoppingList::with('producto')->get();
        return response()->json($lista);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'nullable|integer|min:1',
            'prioridad' => 'nullable|in:Baja,Media,Alta',
        ]);

        $item = ShoppingList::firstOrNew([
            'producto_id' => $request->input('producto_id'),
        ]);

        $item->cantidad = ($item->exists ? $item->cantidad : 0) + $request->input('cantidad', 1);
        $item->prioridad = $request->input('prioridad', $item->prioridad ?? 'Media');
        $item->save();

        $item->load('producto');

        return response()->json([
            'message' => 'Producto actualizado en la lista',
            'data' => $item,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
            'prioridad' => 'sometimes|in:Baja,Media,Alta',
        ]);

        $item = ShoppingList::findOrFail($id);
        $item->update($request->only(['cantidad', 'prioridad']));

        return response()->json($item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        ShoppingList::destroy($id);
        return response()->json(['message' => 'Item eliminado de la lista'], 200);
    }

    public function confirmRestock()
    {
        DB::transaction(function () {
            $items = ShoppingList::whereHas('producto')->with('producto')->get();

            foreach ($items as $item) {
                $item->producto->increment('stock', $item->cantidad);
            }

            ShoppingList::truncate();
        });

        return response()->json(['message' => 'Restock realizado con exito. Inventario actualizado.'], 200);
    }

    public function confirmOrder(Request $request)
    {
        $validated = $request->validate([
            'estado_id' => 'nullable|exists:estados,id',
            'nombre' => 'nullable|string|max:255',
            'direccionEntrega' => 'nullable|string|max:255',
            'fechaEntrega' => 'nullable|date',
        ]);

        try {
            $pedido = DB::transaction(function () use ($validated) {
                $items = ShoppingList::whereHas('producto')
                    ->with('producto')
                    ->lockForUpdate()
                    ->get();

                if ($items->isEmpty()) {
                    throw new \Exception('El carrito esta vacio');
                }

                $estado = isset($validated['estado_id'])
                    ? Estado::findOrFail($validated['estado_id'])
                    : Estado::whereRaw('LOWER(nombre) like ?', ['%confirm%'])->first();

                if (!$estado) {
                    $estado = Estado::first();
                }

                if (!$estado) {
                    $estado = Estado::create(['nombre' => 'Confirmado']);
                }

                $direccionEntrega = $validated['direccionEntrega'] ?? null;

                if (!$direccionEntrega) {
                    $direccionEntrega = 'Almacen central';
                }

                $pedido = Pedido::create([
                    'nombre' => $validated['nombre'] ?? 'Administrador',
                    'tipo' => 'reposicion',
                    'cliente_id' => null,
                    'estado_id' => $estado->id,
                    'direccionEntrega' => $direccionEntrega,
                    'fechaPedido' => Carbon::now(),
                    'fechaEntrega' => $validated['fechaEntrega'] ?? null,
                ]);

                foreach ($items as $item) {
                    $producto = $item->producto;

                    if (!$producto) {
                        continue;
                    }

                    $precioUnidad = (float) $producto->precio;

                    LineaVenta::create([
                        'pedido_id' => $pedido->id,
                        'producto_id' => $producto->id,
                        'cantidad' => $item->cantidad,
                        'precioUnidad' => $precioUnidad,
                        'precioTotal' => $precioUnidad * $item->cantidad,
                    ]);

                    $producto->increment('stock', $item->cantidad);
                }

                ShoppingList::whereIn('id', $items->pluck('id'))->delete();

                return $pedido->load(['cliente', 'estado', 'lineasVenta.producto']);
            });

            return response()->json([
                'message' => 'Pedido de reposicion creado correctamente. Stock actualizado',
                'data' => $pedido,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
