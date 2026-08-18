<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Prueba Técnica - Productos</title>
</head>

<body>
    <h1>Listado de Productos</h1>

    @if ($products->isEmpty())
        <p>No hay productos en el sistema actualmente.</p>
    @else
        <ul>
            @foreach ($products as $product)
                <li>
                    <strong>{{ $product->name }}</strong> - ${{ number_format($product->price, 2) }}
                </li>
            @endforeach
        </ul>
    @endif

    <hr>
    <h2>Simulador de API (Probar Funcionalidades sin Postman)</h2>

    <!-- Formulario 1️⃣ Crear Producto -->
    <h3>1️⃣ Crear Producto (POST /api/products)</h3>
    <form id="formProduct">
        <input type="text" id="p_name" placeholder="Nombre del producto" required>
        <input type="number" id="p_price" step="0.01" placeholder="Precio (ej: 10.50)" required>
        <button type="submit">Enviar a la API</button>
    </form>

    <!-- Formulario 3️⃣ Crear Pedido -->
    <h3>3️⃣ Crear Pedido (POST /api/orders)</h3>
    <form id="formOrder">
        <input type="number" id="o_user" placeholder="ID de Usuario (ej: 1)" required>
        <p><small>Ingresa los productos en formato JSON abajo:</small></p>
        <textarea id="o_json" rows="6" cols="40">[{"product_id": 1, "quantity": 2}]</textarea>
        <br>
        <button type="submit">Crear Pedido en Backend</button>
    </form>

    <h3>Resultado de la API:</h3>
    <pre id="apiResponse" style="background: #f0f0f0; padding: 10px; border: 1px solid #ccc;">Aquí se mostrará la respuesta JSON de tu backend...</pre>

    <script>
        // JS para probar Crear Producto
        document.getElementById('formProduct').addEventListener('submit', async (e) => {
            e.preventDefault();
            const response = await fetch('/api/products', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: document.getElementById('p_name').value,
                    price: document.getElementById('p_price').value
                })
            });
            const data = await response.json();
            document.getElementById('apiResponse').innerText = JSON.stringify(data, null, 2);
        });

        // JS para probar Crear Pedido
        document.getElementById('formOrder').addEventListener('submit', async (e) => {
            e.preventDefault();
            const response = await fetch('/api/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    user_id: document.getElementById('o_user').value,
                    products: JSON.parse(document.getElementById('o_json').value)
                })
            });
            const data = await response.json();
            document.getElementById('apiResponse').innerText = JSON.stringify(data, null, 2);
        });
    </script>
</body>

</html>
