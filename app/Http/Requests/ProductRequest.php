<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'stock' => 'required|integer|min:0',
        ];
    }

    /**
     * Mensajes de error personalizados para la validación del producto.
     */
    public function messages(): array
    {
        return [
            'name.required'  => 'El nombre del producto es un campo obligatorio.',
            'name.string'    => 'El nombre del producto debe ser un texto válido.',
            'name.max'       => 'El nombre del producto no puede superar los 255 caracteres.',

            'price.required' => 'El precio es obligatorio.',
            'price.numeric'  => 'El precio debe ser un valor numérico decimal.',
            'price.min'      => 'El precio del producto debe ser un número mayor a 0.01.',

            'stock.required' => 'El stock / inventario inicial es obligatorio.',
            'stock.integer'  => 'El stock debe ser un número entero (sin decimales).',
            'stock.min'      => 'El stock no puede ser un número negativo.',
        ];
    }
}
