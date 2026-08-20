<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    public function store(OrderRequest $request)
    {
        // 🆔 1. CONTROL DE IDEMPOTENCIA
        $idempotencyKey = $request->header('X-Idempotency-Key');

        if ($idempotencyKey) {
            $lock = Cache::lock("order_processing:{$idempotencyKey}", 300);

            if (!$lock->get()) {
                return response()->json([
                    'message' => 'Esta transacción ya está siendo procesada. Por favor, espere.'
                ], 422);
            }
        }

        try {
            $validatedData = $request->validated();

            // 🏁 2. TRANSACCIÓN AVANZADA
            $order = DB::transaction(function () use ($validatedData, $request) {
                $total = 0;
                $pivotData = [];

                foreach ($validatedData['products'] as $item) {
                    if ((int) $item['quantity'] <= 0) {
                        continue;
                    }

                    // 🔒 Bloqueo pesimista a nivel de fila en la BD
                    $product = Product::where('id', $item['product_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    // 🛑 VALIDACIÓN DE STOCK: Evita vender lo que no hay
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stock insuficiente para el producto: {$product->name}");
                    }

                    $subtotal = $product->price * $item['quantity'];
                    $total += $subtotal;

                    $pivotData[$item['product_id']] = [
                        'quantity' => $item['quantity'],
                        'price'    => $product->price, // Conserva el precio histórico
                        'subtotal' => $subtotal,
                    ];

                    // 📉 DESCUENTO DE STOCK: Operación segura dentro de la transacción
                    $product->decrement('stock', $item['quantity']);
                }

                // 🔐 Creación segura del pedido vinculada al usuario autenticado
                $order = Order::create([
                    'user_id' => $request->user()->id ?? $request->user_id,
                    'total'   => $total,
                ]);

                // Guardamos la relación en la tabla pivote order_product
                $order->products()->attach($pivotData);

                return $order->load(['user', 'products']);
            });

            return (new OrderResource($order))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        } finally {
            if (isset($lock)) {
                $lock->release();
            }
        }
    }
}
