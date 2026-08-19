<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('products')) {
            $filteredProducts = array_filter($this->input('products'), function ($item) {
                return isset($item['quantity']) && (int)$item['quantity'] > 0;
            });

            $this->merge([
                'products' => array_values($filteredProducts)
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id|distinct',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }
}
