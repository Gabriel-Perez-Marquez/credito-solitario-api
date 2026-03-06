<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Producto;
use App\Models\LineaVenta;
use App\Models\Cliente;
use Carbon\Carbon;
use App\Models\Estado;
use App\Models\Notificacion;

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

        if ($pedido->wasChanged('estado_id')) {
            
            if ($pedido->estado && $pedido->estado->nombre === 'Finalizado') {
                
                if ($pedido->cliente && $pedido->cliente->user_id) {
                    \App\Models\Notificacion::create([
                        'user_id' => $pedido->cliente->user_id,
                        'titulo'  => '¡Pedido Finalizado! 🎉',
                        'mensaje' => "Tu pedido #{$pedido->id} ya está listo y ha sido finalizado.",
                        'leida'   => false,
                    ]);
                }
            }
        }

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


    public function checkoutVenta(Request $request)
    {
        
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'lineas' => 'required|array|min:1', 
            'lineas.*.producto_id' => 'required|exists:productos,id',
            'lineas.*.cantidad' => 'required|integer|min:1',
        ]);

        try {
            
            $pedido = DB::transaction(function () use ($validated) {
                

                $cliente = Cliente::with('direccion')->findOrFail($validated['cliente_id']);
            
                $direccionString = 'Dirección no especificada';
                if ($cliente->direccion) {
                    $direccionString = $cliente->direccion->calle . ' ' . 
                                    $cliente->direccion->numCasa . ', ' . 
                                    $cliente->direccion->municipio . ' (' . 
                                    $cliente->direccion->provincia . ')';
                }
                

                $estado = Estado::firstOrCreate(['nombre' => 'Pendiente']);
                

                $pedido = Pedido::create([
                    'tipo' => 'venta',
                    'cliente_id' => $validated['cliente_id'],
                    'estado_id' => $estado->id,
                    'direccionEntrega' => $direccionString,
                    'fechaPedido' => Carbon::now(),
                ]);

                $costeTotalPedido = 0;
                
                foreach ($validated['lineas'] as $linea) {
                    
                    $producto = Producto::lockForUpdate()->findOrFail($linea['producto_id']);

                    
                    if ($producto->stock < $linea['cantidad']) {
                        throw new \Exception("Stock insuficiente para: {$producto->nombre}");
                    }

                    $precioUnidad = $producto->precio;
                    $costeLinea = $precioUnidad * $linea['cantidad'];
                    
                    LineaVenta::create([
                        'pedido_id' => $pedido->id,
                        'producto_id' => $producto->id,
                        'cantidad' => $linea['cantidad'],
                        'precioUnidad' => $precioUnidad,
                        'precioTotal' => $precioUnidad * $linea['cantidad'],
                    ]);

                    
                    $producto->decrement('stock', $linea['cantidad']);

                    $costeTotalPedido += $costeLinea;
                }

                if ($cliente->saldo < $costeTotalPedido) {
                    throw new \Exception("Saldo insuficiente. El pedido cuesta {$costeTotalPedido} pts y tienes {$cliente->saldo} pts.");
                }

                $cliente->decrement('saldo', $costeTotalPedido);

                return $pedido->load(['lineasVenta.producto']);
            });

            return response()->json([
                'message' => 'Compra realizada con éxito',
                'pedido' => $pedido
            ], 201);

        } catch (\Exception $e) {
           
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    /**
     * Obtiene el historial de pedidos del usuario autenticado.
     */
    public function misPedidos(Request $request)
    {
        $user = $request->user();

        // Verificamos si el usuario tiene un perfil de cliente asociado
        if (!$user->cliente) {
            return response()->json([], 200); // Si no tiene cliente, no hay pedidos
        }

        // Buscamos los pedidos
        $pedidos = $user->cliente->pedidos()
            ->with(['estado', 'lineasVenta.producto'])
            ->orderBy('fechaPedido', 'desc') 
            ->get();

        return response()->json($pedidos, 200);
    }
}
