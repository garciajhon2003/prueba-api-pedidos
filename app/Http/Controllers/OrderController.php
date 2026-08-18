<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // POST /api/orders -> Crea el pedido, calcula el total y devuelve JSON
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id|distinct',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $total = 0;
            $pivotData = [];

            foreach ($request->products as $item) {
                $product = \App\Models\Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $pivotData[$item['product_id']] = [
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal
                ];
            }

            // Crear el pedido con el total calculado
            $order = \App\Models\Order::create([
                'user_id' => $request->user_id,
                'total' => $total,
            ]);

            // Guardar en la tabla pivote order_product incluyendo los campos extras
            $order->products()->attach($pivotData);

            return response()->json($order->load('products'), 201);
        });
    }

    public function show($id)
    {
        // Buscamos el pedido con su usuario y sus productos relacionados
        $order = \App\Models\Order::with(['user', 'products'])->findOrFail($id);

        // Retornamos el JSON con la estructura exacta que pide la prueba
        return response()->json([
            'id' => $order->id,
            'user' => $order->user,
            'total' => $order->total,
            'products' => $order->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $product->pivot->quantity,
                    'subtotal' => $product->pivot->subtotal
                ];
            })
        ], 200);
    }
}
