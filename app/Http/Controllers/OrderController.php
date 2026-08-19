<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(OrderRequest $request)
    {
        $validatedData = $request->validated();

        $order = DB::transaction(function () use ($validatedData, $request) {
            $total = 0;
            $pivotData = [];

            foreach ($validatedData['products'] as $item) {
                if ((int) $item['quantity'] <= 0) {
                    continue;
                }

                $product = Product::where('id', $item['product_id'])->lockForUpdate()->firstOrFail();

                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $pivotData[$item['product_id']] = [
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            $order = Order::create([
                'user_id' => $request->user_id,
                'total' => $total,
            ]);

            $order->products()->attach($pivotData);

            return $order->load(['user', 'products']);
        });

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    public function show($id)
    {
        // El manejo de errores de "no encontrado" ahora lo maneja Laravel devolviendo un JSON limpio si usas FindOrFail
        $order = Order::with(['user', 'products'])->findOrFail($id);

        // 3. Devolvemos el recurso en lugar de armar el array manual
        return new OrderResource($order);
    }
}
