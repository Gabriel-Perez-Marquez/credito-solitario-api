<?php
require __DIR__ . '/auth.php';

use App\Http\Controllers\SecuenciaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\LineaVentaController;
use App\Http\Controllers\ImagenProductoController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\Auth\ChangePasswordController;


Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::middleware('auth:sanctum')->post('/logout', [AuthenticatedSessionController::class, 'destroy']);

Route::middleware('auth:sanctum')->post('/change-password', [ChangePasswordController::class, 'update']);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('shopping-list/confirm-order', [ShoppingListController::class, 'confirmOrder']);
    Route::post('shopping-list/confirm-restock', [ShoppingListController::class, 'confirmRestock']);
    Route::post('carrito/confirm-order', [ShoppingListController::class, 'confirmOrder']);
    Route::post('carrito/confirm-restock', [ShoppingListController::class, 'confirmRestock']);
    Route::post('carrito/checkout-movil', [PedidoController::class, 'checkoutVenta']);

    Route::apiResource('secuencias', SecuenciaController::class);
    Route::apiResource('administradores', AdministradorController::class);
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('productos', ProductoController::class);
    Route::apiResource('pedidos', PedidoController::class);
    Route::apiResource('facturas', FacturaController::class);
    Route::apiResource('categorias', CategoriaController::class);
    Route::apiResource('direcciones', DireccionController::class);
    Route::apiResource('estados', EstadoController::class);
    Route::apiResource('linea-ventas', LineaVentaController::class);
    Route::apiResource('carrito', ShoppingListController::class);
    Route::apiResource('shopping-list', ShoppingListController::class);

    
});
