<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;

Route::view('/', 'welcome')->name('home');

Route::get('/dashboard', function () {
    $products = \App\Models\Product::select('name', 'price')->get();
    return view('dashboard', compact('products'));
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__ . '/settings.php';
