<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ProductWebController;
use App\Http\Controllers\Web\OrderWebController;

use App\Models\Product;

Route::get('/', function () {
    $products = Product::all();
    return view('dashboard', compact('products'));
})->middleware(['auth'])->name('dashboard');

Route::redirect('/home', '/')->name('home');

// 🖥️ FLUJO DE PRODUCTOS (Web) - Corregido a ->name()
Route::get('/products', [ProductWebController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductWebController::class, 'create'])->name('products.create');
Route::post('/products', [ProductWebController::class, 'store'])->name('products.store');

// 🖥️ FLUJO DE PEDIDOS/ORDERS (Web) - Corregido a ->name() y ->show
Route::get('/orders', [OrderWebController::class, 'index'])->name('orders.index');
Route::get('/orders/create', [OrderWebController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrderWebController::class, 'store'])->name('orders.store');
Route::get('/orders/{id}', [OrderWebController::class, 'show'])->name('orders.show');

require __DIR__ . '/settings.php';
