<?php

namespace App\Http\Requests\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;

class UpdateUserProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()?->is_seller;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|nullable|numeric|min:0',
            'discount_price' => 'sometimes|nullable|numeric|min:0|lt:price',
            'stock' => 'sometimes|nullable|integer|min:0',
            'cover_image' => 'sometimes|nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'images.*' => 'sometimes|nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'remove_image_ids' => 'sometimes|nullable|array',
            'remove_image_ids.*' => 'sometimes|nullable|integer|exists:user_product_images,id',
        ];
    }

    public function messages(): array
    {
        return [
            'discount_price.lt' => 'The discount price must be less than the regular price.',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            $request = $this;
            $fieldsToCheck = [
                'title', 'description', 'price', 'discount_price', 'stock', 'cover_image', 'images', 'remove_image_ids'
            ];

            $hasAtLeastOneField = collect($fieldsToCheck)->contains(function ($field) use ($request) {
                return $request->filled($field) || $request->hasFile($field);
            });

            if (!$hasAtLeastOneField) {
                $validator->errors()->add('empty', 'You must provide at least one of the fields or images to update.');
            }
        });
    }
}
