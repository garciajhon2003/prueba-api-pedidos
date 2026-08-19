<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Http\Requests\OrderRequest; // 1. Importamos tu FormRequest
use App\Http\Resources\OrderResource; // 2. Importamos el Resource (lo crearemos abajo)
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Cambiamos Request por tu nuevo OrderRequest (Validación y filtro automáticos)
    public function store(OrderRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $productIds = collect($request->products)->pluck('product_id')->toArray();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $total = 0;
            $pivotData = [];

            foreach ($request->products as $item) {
                $product = $products->get($item['product_id']);

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $pivotData[$item['product_id']] = [
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal
                ];
            }

            $order = Order::create([
                'user_id' => $request->user_id,
                'total' => $total,
            ]);

            $order->products()->attach($pivotData);

            // 3. Devolvemos la respuesta usando el Resource
            return new OrderResource($order->load(['user', 'products']), 201);
        });
    }

    public function show($id)
    {
        // El manejo de errores de "no encontrado" ahora lo maneja Laravel devolviendo un JSON limpio si usas FindOrFail
        $order = Order::with(['user', 'products'])->findOrFail($id);

        // 3. Devolvemos el recurso en lugar de armar el array manual
        return new OrderResource($order);
    }
}
