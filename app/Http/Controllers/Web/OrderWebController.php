<?php

namespace App\Http\Controllers\Web;

use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;

class OrderWebController extends Controller
{
    // Muestra el listado principal de pedidos
    public function index()
    {
        $orders = Order::with('user')->get();
        return view('orders.index', compact('orders'));
    }

    // Dirige al formulario de creación
    public function create()
    {
        $users = User::all();
        $products = Product::all();
        return view('orders.create', compact('users', 'products'));
    }

    // Recibe el formulario, calcula totales en backend, descuenta stock de forma segura y guarda
    public function store(OrderRequest $request)
    {
        $validatedData = $request->validated();

        try {
            $order = DB::transaction(function () use ($validatedData, $request) {
                $total = 0;
                $pivotData = [];

                foreach ($validatedData['products'] as $item) {
                    if ((int) $item['quantity'] <= 0) {
                        continue;
                    }

                    $product = Product::where('id', $item['product_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    // 🛑 VALIDACIÓN DE STOCK: Alerta si el administrador intenta vender más de lo que hay
                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stock insuficiente para el producto: {$product->name}. Quedan disponibles: {$product->stock} unidades.");
                    }

                    $subtotal = $product->price * $item['quantity'];
                    $total += $subtotal;

                    $pivotData[$item['product_id']] = [
                        'quantity' => $item['quantity'],
                        'price'    => $product->price, // Conserva precio histórico
                        'subtotal' => $subtotal
                    ];

                    // 📉 DESCUENTO DE STOCK: Operación protegida por la transacción
                    $product->decrement('stock', $item['quantity']);
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
            throw ValidationException::withMessages([
                'products' => $e->getMessage(),
            ]);
        }
    }

    // Muestra la pantalla de detalles de un pedido específico
    public function show($id)
    {
        $order = Order::with(['user', 'products'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }
}
