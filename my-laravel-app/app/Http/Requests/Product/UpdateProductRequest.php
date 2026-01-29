<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product');

        return auth()->check()
            && $product
            && auth()->id() === $product->user_id;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'max:255'],
            'description' => ['nullable', 'max:1000'],
            'price'       => ['required', 'integer', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'image'       => ['nullable', 'image', 'max:2048'],
        ];
    }
}
