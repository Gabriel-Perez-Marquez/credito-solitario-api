<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/productos', [ProductoController::class, 'index']);
Route::get('/productos/nuevo', [ProductoController::class, 'index']);

require __DIR__.'/auth.php';
