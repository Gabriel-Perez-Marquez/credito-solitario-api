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

        $item = ShoppingList::updateOrCreate(
            ['producto_id' => $request->producto_id], 
            [
                'cantidad' => DB::raw("cantidad + " . $request->input('cantidad', 1)),
                'prioridad' => $request->input('prioridad', 'Media') 
            ]
        );

        $item->load('producto');

        return response()->json([
            'message' => 'Producto actualizado en la lista',
            'data' => $item
        ], 200);
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

        return response()->json(['message' => 'Restock realizado con éxito. Inventario actualizado.'], 200);
    }
}