<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    // Un pedido pertenece a un usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Un pedido tiene muchos productos
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_product')
            ->withPivot('quantity', 'price', 'subtotal')
            ->withTimestamps();
    }

    /**
     * Calcula automáticamente el total del pedido basado en sus productos y cantidades.
     * Puedes llamar a este método antes de guardar el pedido en tu controlador.
     */
    public function calculateTotal(): void
    {
        $this->total = $this->products->sum(function ($product) {
            return $product->price * $product->pivot->quantity;
        });
    }
}
