<x-layouts::app :title="__('Dashboard')">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Productos Disponibles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">

                <h3 class="text-lg font-bold mb-4">Listado de Productos</h3>

                @if ($products->isEmpty())
                    <p class="text-gray-500">No hay productos registrados en el sistema actualmente.</p>
                @else
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($products as $product)
                            <li>
                                <strong>{{ $product->name }}</strong> - ${{ number_format($product->price, 2) }}
                            </li>
                        @endforeach
                    </ul>
                @endif

            </div>
        </div>
    </div>
</x-layouts::app>
