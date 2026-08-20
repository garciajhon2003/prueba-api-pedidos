<x-layouts::app>
    <div class="flex justify-between items-center mb-4">
        <flux:breadcrumbs class="mb-4">
            <flux:breadcrumbs.item href="{{ route('products.index') }}">
                Productos
            </flux:breadcrumbs.item>
        </flux:breadcrumbs>

        <a class="btn btn-blue text-xs" href="{{ route('products.create') }}">
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
                        Precio
                    </th>
                    <th>
                        Stock
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="bg-neutral-primary border-b border-default">
                        <td class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                            {{ $product->id }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $product->name }}
                        </td>
                        <td class="px-6 py-4">
                            ${{ number_format($product->price, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $product->stock }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center">
                            No hay productos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</x-layouts::app>
