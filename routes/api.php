<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

// 1️⃣ Crear un producto
Route::post('/products', [ProductController::class, 'store']);

// 2️⃣ Listar productos
Route::get('/products', [ProductController::class, 'index']);

// 3️⃣ Crear un pedido
Route::post('/orders', [OrderController::class, 'store']);

// 4️⃣ Obtener los productos de un pedido
Route::get('/orders/{id}', [OrderController::class, 'show']);
