<?php

use App\Models\User;
use App\Models\Product;
use function Pest\Laravel\{postJson, assertDatabaseHas};

it('creates an order via API and calculates the total correctly', function () {
    // 1. Crear datos ficticios en la base de datos de pruebas
    $user = User::factory()->create();
    $product1 = Product::factory()->create(['price' => 10.00]); // 10.00 x 2 = 20.00
    $product2 = Product::factory()->create(['price' => 15.50]); // 15.50 x 3 = 46.50

    $expectedTotal = 66.50;

    // 2. Simular la petición POST a la API
    $response = postJson('/api/orders', [
        'user_id' => $user->id,
        'products' => [
            ['product_id' => $product1->id, 'quantity' => 2],
            ['product_id' => $product2->id, 'quantity' => 3],
        ],
    ]);

    // 3. Comprobar que la respuesta sea 201 (Creado)
    $response->assertStatus(201);

    // 4. Asegurar que guardó el total exacto en la tabla orders
    assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'total' => $expectedTotal,
    ]);

    // 5. Asegurar que guardó los precios históricos en la pivote
    assertDatabaseHas('order_product', [
        'product_id' => $product1->id,
        'price' => 10.00,
        'quantity' => 2,
        'subtotal' => 20.00,
    ]);
});
