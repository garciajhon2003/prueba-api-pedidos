<x-layouts::app>
    <div class="flex justify-between items-center mb-4">
        <flux:breadcrumbs class="mb-4">
            <flux:breadcrumbs.item href="{{ route('orders.index') }}">
                Pedido
            </flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <a class="btn btn-blue text-xs" href="{{ route('orders.create') }}">
            Nuevo producto
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
                        Nombre
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Cantidad
                    </th>
                    <th scope="col" class="px-6 py-3 font-medium">
                        Ver Detalles
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
                        <td class="px-6 py-4"> <!-- Enlace a los detalles -->
                            <a href="{{ route('orders.show', $order->id) }}"
                                class="text-blue-600 hover:underline font-medium">
                                Ver Detalles
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center">
                            No hay pedidos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts::app>
