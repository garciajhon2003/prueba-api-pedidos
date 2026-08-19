<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array|min:1',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.product_id' => 'exclude_if:products.*.quantity,0|required|exists:products,id|distinct',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'El usuario seleccionado no existe en el sistema.',
            'products.*.product_id.exists' => 'El producto seleccionado no existe en nuestro catálogo.',
            'products.*.product_id.distinct' => 'Este producto ya fue incluido en el pedido.',
            'products.*.quantity.min' => 'La cantidad debe ser como mínimo 1.',
        ];
    }
}
