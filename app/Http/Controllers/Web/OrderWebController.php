<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderWebController extends Controller
{
    // Muestra el listado principal de pedidos (el index que ya tienes)
    public function index()
    {
        // Cargamos con el usuario para mostrar quién hizo el pedido en la lista
        $orders = Order::with('user')->get();
        return view('orders.index', compact('orders'));
    }

    // Dirige al formulario de creación (inyectando usuarios y productos para los select)
    public function create()
    {
        $users = User::all();
        $products = Product::all();
        return view('orders.create', compact('users', 'products'));
    }

    // Recibe el formulario, calcula totales en backend y guarda
    public function store(Request $request)
    {
        if ($request->has('products')) {
            $filteredProducts = array_filter($request->input('products'), function ($item) {
                // Solo dejamos pasar los productos cuya cantidad ingresada sea mayor a 0
                return isset($item['quantity']) && (int)$item['quantity'] > 0;
            });

            // Sobreescribimos el input 'products' con el nuevo arreglo limpio
            $request->merge(['products' => array_values($filteredProducts)]);
        }


        $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array|min:1', // Exige que al menos el usuario compre 1 producto en total
            'products.*.product_id' => 'required|exists:products,id|distinct',
            'products.*.quantity' => 'required|integer|min:1', // Cada uno de los que sí se van a comprar debe ser >= 1
        ]);

        try {
            $order = DB::transaction(function () use ($request) {
                $total = 0;
                $pivotData = [];

                foreach ($request->products as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $subtotal = $product->price * $item['quantity'];
                    $total += $subtotal;

                    $pivotData[$item['product_id']] = [
                        'quantity' => $item['quantity'],
                        'price'    => $product->price,
                        'subtotal' => $subtotal
                    ];
                }

                $order = Order::create([
                    'user_id' => $request->user_id,
                    'total' => $total,
                ]);

                $order->products()->attach($pivotData);

                return $order;
            });

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Pedido registrado con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al procesar el pedido: ' . $e->getMessage());
        }
    }


    // Muestra la pantalla de detalles de un pedido específico
    public function show($id)
    {
        $order = Order::with(['user', 'products'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }
}
