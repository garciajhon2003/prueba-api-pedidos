<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    // POST /api/products -> Crea el producto usando el Request validado y responde con el Resource
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());

        // 3. Devolvemos la respuesta formateada de forma profesional con código 201
        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    // GET /api/products -> Devuelve el listado transformado por el Resource con código 200
    public function index()
    {
        $products = Product::select('id', 'name', 'price')->get();

        // 4. Usamos ::collection() para transformar un listado completo de productos
        return ProductResource::collection($products);
    }
}
