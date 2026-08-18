<x-layouts::app>
    <div class="flex justify-between items-center mb-4">
        <flux:breadcrumbs class="mb-4">
            <!-- 1️⃣ Corregido: Apunta al listado web de pedidos -->
            <flux:breadcrumbs.item href="{{ route('orders.index') }}">
                Pedidos
            </flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <!-- 2️⃣ Corregido: Apunta al formulario web de creación y ajustado el texto del botón -->
        <a class="btn btn-blue text-xs" href="{{ route('orders.create') }}">
            Nuevo Pedido
        </a>
    </div>

    <div class="relative overflow-x-auto bg-neutral-primary-soft shadow-xs rounded-base border border-default">
        <table class="w-full text-sm text-left rtl:text-right text-body">
            <thead class="text-sm text-body bg-neutral-secondary-soft border-b rounded-base border-default">
                <tr>
                    <th scope="col" class="px-6 py-3 font-medium">
                        ID
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Usuario
                    </th>
                    <!-- 3️⃣ Corregido: El encabezado ahora dice "Total" en vez de "Cantidad" -->
                    <th scope="col" class="px-6 py-3 font-medium">
                        Total
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="bg-neutral-primary border-b border-default">
                        <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            {{ $order->id }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $order->user->name }}
                        </td>
                        <td class="px-6 py-4">
                            ${{ number_format($order->total, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <!-- 4️⃣ Corregido: Apunta al controlador web de detalles del pedido -->
                            <a href="{{ route('orders.show', $order->id) }}"
                                class="text-blue-600 hover:underline font-medium">
                                Ver Detalles
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <!-- 5️⃣ Corregido: Cambiado colspan a 4 para abarcar todas las columnas de la tabla -->
                        <td colspan="4" class="px-6 py-4 text-center">
                            No hay pedidos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts::app>
