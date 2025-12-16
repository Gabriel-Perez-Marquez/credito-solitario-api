<?php
require __DIR__ . '/auth.php';

use App\Http\Controllers\SecuenciaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
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
    Route::apiResource('imagenes-producto', ImagenProductoController::class);
});
