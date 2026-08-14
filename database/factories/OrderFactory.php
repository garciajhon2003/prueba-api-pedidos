<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Muestra el formulario para crear un pedido seleccionando productos existentes
    public function create()
    {
        $products = Product::all();

        // Si no hay usuarios en la BD, creamos uno rápido para que el formulario no falle
        $user = User::firstOrCreate([
            'email' => 'evaluador@prueba.com'
        ], [
            'name' => 'Usuario Evaluador'
        ]);

        return view('orders.create', compact('products', 'user'));
    }

    // 3️⃣ Crear un pedido y calcular el total automáticamente en el backend
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $order = DB::transaction(function () use ($request) {
            $order = Order::create([
                'user_id' => $request->user_id,
                'total' => 0.00,
            ]);

            foreach ($request->products as $productData) {
                // Solo adjuntar si la cantidad es mayor que cero para evitar entradas vacías en el HTML
                if ($productData['quantity'] > 0) {
                    $order->products()->attach($productData['product_id'], [
                        'quantity' => $productData['quantity']
                    ]);
                }
            }

            $order->load('products');
            $order->calculateTotal(); // Tu método del backend
            $order->save();

            return $order;
        });

        return redirect()->route('orders.show', $order->id);
    }

    // 4️⃣ Obtener los productos de un pedido
    public function show($id)
    {
        $order = Order::with(['user', 'products'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }
}
