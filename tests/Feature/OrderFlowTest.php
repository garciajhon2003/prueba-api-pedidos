<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;

it('loads the order creation form with a user and products', function () {
    $user = User::factory()->create();
    Product::factory()->count(3)->create();

    $response = get('/orders/create');

    $response->assertOk()
        ->assertViewHas('user', $user)
        ->assertViewHas('products');
});

it('creates an order and calculates the total from product prices and quantities', function () {
    $user = User::factory()->create();
    $productA = Product::factory()->create(['price' => 25.50]);
    $productB = Product::factory()->create(['price' => 14.75]);

    $response = post('/orders', [
        'user_id' => $user->id,
        'products' => [
            ['product_id' => $productA->id, 'quantity' => 2],
            ['product_id' => $productB->id, 'quantity' => 1],
        ],
    ]);

    $response->assertRedirect();

    $order = Order::query()->where('user_id', $user->id)->latest()->firstOrFail();

    expect((float) $order->total)->toBe(65.75)
        ->and($order->products)->toHaveCount(2);
});
