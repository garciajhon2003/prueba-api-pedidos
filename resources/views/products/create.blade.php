<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Crear Producto</title>
</head>

<body>
    <h1>Crear Nuevo Producto</h1>
    <a href="{{ route('products.index') }}">⬅️ Volver al listado</a>
    <br><br>

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('products.store') }}" method="POST">
        @csrf
        <label>Nombre del Producto:</label><br>
        <input type="text" name="name" value="{{ old('name') }}"><br><br>

        <label>Precio (Decimal mayor a 0):</label><br>
        <input type="number" step="0.01" name="price" value="{{ old('price') }}"><br><br>

        <label>Stock / Inventario Inicial (Número entero):</label><br>
        <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0"><br><br>
        <!-- ----------------------------------------------------- -->

        <button type="submit">Guardar Producto</button>
    </form>
</body>

</html>
