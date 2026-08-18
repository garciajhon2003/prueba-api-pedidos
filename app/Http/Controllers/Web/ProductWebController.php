<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductWebController extends Controller
{
    // Muestra el listado de productos
    public function index()
    {
        $products = Product::paginate(10);
        return view('products.index', compact('products'));
    }

    // Muestra la vista con el formulario para crear un producto
    public function create()
    {
        return view('products.create');
    }

    // Procesa el formulario y redirecciona de vuelta al index
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Producto creado con éxito.');
    }
}
