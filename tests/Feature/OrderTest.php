<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;

test('puede crear un producto mediante la api', function () {
    $response = $this->postJson('/api/products', [
        'name' => 'Mouse Logitech G Pro',
        'price' => 79.99,
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.name', 'Mouse Logitech G Pro')
        ->assertJsonPath('data.price', 79.99);

    $this->assertDatabaseHas('products', [
        'name' => 'Mouse Logitech G Pro',
        'price' => 79.99,
    ]);
});

test('puede crear un pedido y calcula correctamente el total', function () {
    $user = User::factory()->create();
    $keyboard = Product::factory()->create(['price' => 100]);
    $mouse = Product::factory()->create(['price' => 50]);

    $response = $this->postJson('/api/orders', [
        'user_id' => $user->id,
        'products' => [
            ['product_id' => $keyboard->id, 'quantity' => 2],
            ['product_id' => $mouse->id, 'quantity' => 3],
        ],
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.total', '350.00');

    $order = Order::latest('id')->firstOrFail();

    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'user_id' => $user->id,
        'total' => 350,
    ]);

    $this->assertDatabaseHas('order_product', [
        'order_id' => $order->id,
        'product_id' => $keyboard->id,
        'quantity' => 2,
        'price' => 100,
        'subtotal' => 200,
    ]);
});

test('rechaza cantidades menores que uno', function (int $quantity) {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $this->postJson('/api/orders', [
        'user_id' => $user->id,
        'products' => [
            ['product_id' => $product->id, 'quantity' => $quantity],
        ],
    ])->assertUnprocessable();
})->with([0, -1]);

test('rechaza productos duplicados en el mismo pedido', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $this->postJson('/api/orders', [
        'user_id' => $user->id,
        'products' => [
            ['product_id' => $product->id, 'quantity' => 1],
            ['product_id' => $product->id, 'quantity' => 2],
        ],
    ])->assertUnprocessable();
});

test('conserva el precio historico del producto en el pedido', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 150]);

    $response = $this->postJson('/api/orders', [
        'user_id' => $user->id,
        'products' => [
            ['product_id' => $product->id, 'quantity' => 1],
        ],
    ]);

    $response->assertCreated();
    $order = Order::latest('id')->firstOrFail();

    $product->update(['price' => 299.99]);

    $this->assertDatabaseHas('order_product', [
        'order_id' => $order->id,
        'product_id' => $product->id,
        'price' => 150,
        'subtotal' => 150,
    ]);
});
