<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;

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
    public function store(OrderRequest $request)
    {
        // Si el código llega aquí, los datos YA están filtrados y validados correctamente.
        try {
            $order = DB::transaction(function () use ($request) {
                $total = 0;
                $pivotData = [];

                $productIds = collect($request->products)->pluck('product_id')->toArray();
                $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

                foreach ($request->products as $item) {
                    $product = $products->get($item['product_id']);

                    if (!$product) {
                        throw new \Exception("El producto con ID {$item['product_id']} no está disponible.");
                    }

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
        // Buscamos un único pedido con sus relaciones
        $order = Order::with(['user', 'products'])->findOrFail($id);

        return view('orders.show', compact('order'));
    }
}
