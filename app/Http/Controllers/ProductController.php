<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // POST /api/products -> Crea el producto y devuelve JSON con código 201
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
        ]);

        $product = Product::create($validated);

        // ESTO ES LO QUE TE PIDE: Devolver JSON y código 201. Nada de views ni redirects.
        return response()->json($product, 201);
    }

    // GET /api/products -> Debe devolver el listado de productos en JSON con código 200
    public function index()
    {
        $products = Product::select('id', 'name', 'price')->get();

        // Devolvemos la lista limpia en formato JSON
        return response()->json($products, 200);
    }
}
