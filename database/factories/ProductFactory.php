<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // 2️⃣ Listar productos
    public function index()
    {
        if (\App\Models\Product::count() === 0) {
            \App\Models\Product::create(['name' => 'Laptop Core i7', 'price' => 899.99]);
            \App\Models\Product::create(['name' => 'Mouse Óptico', 'price' => 25.50]);
            \App\Models\Product::create(['name' => 'Teclado Mecánico', 'price' => 75.00]);
        }
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    // Muestra el formulario web para crear un producto
    public function create()
    {
        return view('products.create');
    }

    // 1️⃣ Crear un producto desde el formulario web
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|gt:0', // price > 0
        ]);

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return redirect()->route('products.index');
    }
}
