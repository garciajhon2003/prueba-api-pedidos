<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Muestra el listado de todos los pedidos con sus usuarios y productos.
     */
    public function show(Order $order)
    {
        // Cargamos las relaciones necesarias
        $order->load(['user', 'products']);

        return view('components.api.orders.show', compact('order'));
    }

    public function index()
    {
        $users = User::all();
        $orders = Order::latest()->paginate(10);
        return view('components.api.orders.index', compact('orders', 'users'));
    }

    public function create()
    {
        $orders = Order::all();
        $products = Product::all();
        $users = User::all();
        return view('components.api.orders.create', compact('orders', 'products', 'users'));
    }

    /**
     * Crea un nuevo pedido calculando el total automáticamente en el backend.
     */
    public function store(Request $request)
    {
        // 1. Filtramos los productos para eliminar los que no tienen cantidad (vacíos, 0 o menores)
        if ($request->has('products')) {
            $filteredProducts = array_filter($request->input('products'), function ($product) {
                return isset($product['quantity']) && (int)$product['quantity'] > 0;
            });

            // Reindexamos el arreglo para que no queden huecos en las llaves (ej: 0, 2, 5)
            $request->merge(['products' => array_values($filteredProducts)]);
        }

        // 2. Validamos los datos limpios y el ID del usuario
        $data = $request->validate([
            'user_id' => 'required|exists:users,id', // Asegura que el usuario exista
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // 3. Usamos una transacción de Base de Datos
        return DB::transaction(function () use ($data) {

            // Creamos el pedido
            $order = Order::create([
                'user_id' => $data['user_id'],
                'total' => 0,
            ]);

            $total = 0;

            // Adjuntamos solo los productos que pasaron el filtro (cantidad > 0)
            foreach ($data['products'] as $productData) {
                $product = Product::find($productData['product_id']);
                $subtotal = $product->price * $productData['quantity'];

                $order->products()->attach($productData['product_id'], [
                    'quantity' => $productData['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            // Actualizamos el total del pedido
            $order->update(['total' => $total]);

            return redirect()->route('api.orders.index')
                ->with('success', 'Pedido creado exitosamente');
        });
    }
}
