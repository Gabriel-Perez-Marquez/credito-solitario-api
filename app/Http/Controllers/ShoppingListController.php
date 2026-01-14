<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
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
            'prioridad' => 'nullable|in:Baja,Media,Alta'
        ]);

        $item = ShoppingList::where('producto_id', $request->producto_id)->first();

        if ($item) {
            $item->cantidad += $request->input('cantidad', 1);
            $item->save();
        } else {
            $item = ShoppingList::create([
                'producto_id' => $request->producto_id,
                'cantidad' => $request->input('cantidad', 1),
                'prioridad' => $request->input('prioridad', 'Media')
            ]);
        }
        
        $item->load('producto');

        return response()->json([
            'message' => 'Producto añadido a la lista',
            'data' => $item
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
            'prioridad' => 'sometimes|in:Baja,Media,Alta'
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
        $item = ShoppingList::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Item eliminado de la lista correctamente'], 200);
    }

    /**
     * Confirm the restock operation and update product stock.
     */
    public function confirmRestock()
    {
        DB::transaction(function () {
            $items = ShoppingList::with('producto')->get();

            foreach ($items as $item) {
                if ($item->producto) {
                    $item->producto->stock += $item->cantidad;
                    $item->producto->save();
                }
            }

            ShoppingList::truncate();
        });

        return response()->json(['message' => 'Restock realizado con éxito. Stock actualizado.'], 200);
    }
}