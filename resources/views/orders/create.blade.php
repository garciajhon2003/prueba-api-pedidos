<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Pedido</title>
</head>

<body>
    <h1>Crear Nuevo Pedido</h1>
    <!-- 1️⃣ Corregido: Volver al listado web en vez de la API -->
    <a href="{{ route('products.index') }}">⬅️ Volver al listado de productos</a>
    <br><br>

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <!-- 2️⃣ Corregido: El action ahora va al controlador Web -->
    <form action="{{ route('orders.store') }}" method="POST">
        @csrf

        <h3>Selecciona el Usuario:</h3>
        <flux:select name="user_id">
            <flux:select.option value="">-- Selecciona un usuario --</flux:select.option>
            @foreach ($users as $user)
                <flux:select.option value="{{ $user->id }}">{{ $user->name }}</flux:select.option>
            @endforeach
        </flux:select>
        <br>

        <h3>Selecciona los productos y las cantidades:</h3>
        <p><small style="color: gray;">* Solo se procesarán los productos con cantidad mayor a 0.</small></p>

        <table border="1">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio Unitario</th>
                    <th>Cantidad a pedir</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $index => $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>
                            <input type="hidden" name="products[{{ $index }}][product_id]"
                                value="{{ $product->id }}">
                            <!-- Cambiado a min="0" para permitir ignorar productos, pero el controlador filtrará los 0 -->
                            <input type="number" name="products[{{ $index }}][quantity]" value="0"
                                min="0">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>

        <button type="submit">Realizar pedido</button>
    </form>

</body>

</html>
