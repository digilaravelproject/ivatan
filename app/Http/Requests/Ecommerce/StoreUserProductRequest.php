<?php

namespace App\Http\Requests\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Facades\Auth;

class StoreUserProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()?->is_seller;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:user_products,title',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'nullable|integer|min:0',
            'cover_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The product title is required.',
            'title.unique' => 'A product with this title already exists.',
            'price.required' => 'The regular price is required.',
            'discount_price.lt' => 'The discount price must be less than the regular price.',
        ];
    }
}
