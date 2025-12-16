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
    Route::apiResource('categorias', CategoriaController::class);
    Route::apiResource('clientes', ClientesController::class);
    Route::apiResource('')
});
