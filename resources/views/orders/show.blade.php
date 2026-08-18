<x-layouts::app>
    <div class="mb-6">
        <flux:breadcrumbs class="mb-4">
            <!-- 1️⃣ Corregido: Enlace de regreso al flujo Web -->
            <flux:breadcrumbs.item href="{{ route('orders.index') }}">Pedidos</flux:breadcrumbs.item>
            <flux:breadcrumbs.item>Detalle del Pedido #{{ $order->id }}</flux:breadcrumbs.item>
        </flux:breadcrumbs>
    </div>

    <!-- Información General del Pedido -->
    <div class="bg-neutral-primary border border-default rounded-base p-6 mb-6 shadow-xs">
        <h2 class="text-xl font-bold mb-4 text-heading">Resumen del Pedido</h2>
        <p class="text-sm mb-2"><strong>ID del Pedido:</strong> #{{ $order->id }}</p>
        <p class="text-sm mb-2"><strong>Cliente:</strong> {{ $order->user->name }}</p>
        <p class="text-sm mb-2"><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
        <p class="text-lg font-semibold text-blue-600"><strong>Total General:</strong>
            ${{ number_format($order->total, 2) }}</p>
    </div>

    <!-- Tabla de Productos del Pedido -->
    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
        <table class="w-full text-sm text-left text-body">
            <thead class="text-sm text-body bg-neutral-secondary-soft border-b border-default">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">Producto</th>
                    <th scope="col" class="px-6 py-3 font-medium text-center">Cantidad</th>
                    <th scope="col" class="px-6 py-3 text-right">Precio Unitario</th>
                    <th scope="col" class="px-6 py-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->products as $product)
                    <tr class="bg-neutral-primary border-b border-default">
                        <td class="px-6 py-4 font-medium text-heading">
                            {{ $product->name }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{ $product->pivot->quantity }}
                        </td>
                        <!-- 2️⃣ Corregido: Se lee el precio del modelo Product, no de la tabla pivote -->
                        <td class="px-6 py-4 text-right">
                            ${{ number_format($product->price, 2) }}
                        </td>
                        <!-- 3️⃣ Corregido: El subtotal se calcula dinámicamente en el momento -->
                        <td class="px-6 py-4 text-right font-semibold">
                            ${{ number_format($product->price * $product->pivot->quantity, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts::app>
