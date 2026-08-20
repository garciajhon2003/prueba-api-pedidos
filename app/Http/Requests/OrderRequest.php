<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permite usar este validador
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array|min:1',
            'products.*.quantity' => 'required|integer|min:0',
            'products.*.product_id' => 'required|exists:products,id|distinct',
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $products = $this->input('products', []);
            $totalQuantity = collect($products)->sum('quantity');

            if ($totalQuantity <= 0) {
                $validator->errors()->add('products', 'Debe seleccionar al menos un producto con una cantidad mayor a 0 para realizar el pedido.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'user_id.required'                 => 'El campo de usuario es obligatorio.',
            'user_id.exists'                   => 'El usuario seleccionado no existe.',
            'products.required'                => 'Debe incluir al menos un producto.',
            'products.*.product_id.required'   => 'El producto es obligatorio.',
            'products.*.product_id.exists'     => 'El producto seleccionado no existe en el catálogo.',
            'products.*.product_id.distinct'   => 'productos duplicados',

            'products.*.quantity.required'     => 'La cantidad es obligatoria.',
            'products.*.quantity.integer'      => 'La cantidad debe ser un número entero.',
            'products.*.quantity.min'          => 'La cantidad no puede ser menor a 0.',
        ];
    }
}
