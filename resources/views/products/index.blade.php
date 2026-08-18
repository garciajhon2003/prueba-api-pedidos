<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Productos</title>
</head>
<body>
    <h1>Productos Disponibles</h1>

    @if($products->isEmpty())
        <p>No hay productos registrados.</p>
    @else
        <ul>
            @foreach($products as $product)
                <li>
                    <strong>{{ $product->name }}</strong> - ${{ number_format($product->price, 2) }}
                </li>
            @endforeach
        </ul>
    @endif
</body>
</html>
