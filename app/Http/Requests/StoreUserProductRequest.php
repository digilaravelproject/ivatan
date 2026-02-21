<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;


class StoreUserProductRequest extends FormRequest
{
    /**
     * Authorize only authenticated users who are sellers.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()?->is_seller;
    }

    /**
     * Define validation rules for product creation.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $allowedImageMimes = ['jpeg', 'jpg', 'png', 'webp'];

        return [
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'numeric', 'min:0'],
            'stock'        => ['nullable', 'integer', 'min:0'],

            'cover_image'  => [
                'nullable',
                'file',
                'mimes:' . implode(',', $allowedImageMimes),
                'max:5120', // 5MB
            ],

            'images'       => ['nullable', 'array'],
            'images.*'     => [
                'file',
                'mimes:' . implode(',', $allowedImageMimes),
                'max:5120', // 5MB per image
            ],
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required'        => 'The product title is required.',
            'title.string'          => 'The product title must be a valid string.',
            'title.max'             => 'The title may not exceed 255 characters.',

            'price.required'        => 'Please enter a price for the product.',
            'price.numeric'         => 'The price must be a number.',
            'price.min'             => 'The price must be at least 0.',

            'stock.integer'         => 'Stock must be a whole number.',
            'stock.min'             => 'Stock cannot be negative.',

            'cover_image.file'      => 'The cover image must be a valid file.',
            'cover_image.mimes'     => 'Cover image must be in jpeg, jpg, png, or webp format.',
            'cover_image.max'       => 'Cover image must not exceed 5MB.',

            'images.array'          => 'Images must be an array of files.',
            'images.*.file'         => 'Each uploaded image must be a valid file.',
            'images.*.mimes'        => 'Each image must be in jpeg, jpg, png, or webp format.',
            'images.*.max'          => 'Each image must not exceed 5MB.',
        ];
    }
}
