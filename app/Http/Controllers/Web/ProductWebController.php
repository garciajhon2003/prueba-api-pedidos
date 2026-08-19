<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\ProductRequest;

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
    public function store(ProductRequest $request)
    {
        // Si el código llega aquí, los datos ya pasaron la validación con éxito
        Product::create($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'Producto creado con éxito.');
    }
}
