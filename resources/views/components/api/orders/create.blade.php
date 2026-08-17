<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Pedido</title>
</head>

<body>
    <h1>Crear Nuevo Pedido</h1>
    <a href="{{ route('api.products.index') }}">⬅️ Volver al listado de productos</a>
    <br><br>

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('api.orders.store') }}" method="POST">
        @csrf
        <h3>Selecciona los productos y las cantidades:</h3>
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
                            <input type="number" name="products[{{ $index }}][quantity]" value="0"
                                min="0">
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
        <flux:select name="user_id">
            @foreach ($users as $user)
                <flux:select.option value="{{ $user->id }}">{{ $user->name }}</flux:select.option>
            @endforeach
        </flux:select>
        <button type="submit">Realizar pedido</button>
    </form>

</body>

</html>
