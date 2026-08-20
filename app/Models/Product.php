<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'stock',
    ];

    // Mutador opcional para asegurar que el precio se guarde como flotante/decimal
    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Un producto pertenece a muchos pedidos
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_product')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
