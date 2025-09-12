<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_seller;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Allowed mime types — only formats suitable for ecommerce product images
        $allowedImageMimes = ['jpeg', 'jpg', 'png', 'webp'];

        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'stock'       => ['nullable', 'integer', 'min:0'],

            'cover_image' => [
                'nullable',
                'file',
                'mimes:' . implode(',', $allowedImageMimes),
                'max:5120', // max 5MB
            ],

            'images'     => ['nullable', 'array'],
            'images.*'   => [
                'file',
                'mimes:' . implode(',', $allowedImageMimes),
                'max:5120',
            ],
        ];
    }
    public function messages(): array
    {
        return [
            'cover_image.mimes' => 'Cover image must be a file of type: jpeg, jpg, png, or webp.',
            'images.*.mimes'    => 'Each image must be a file of type: jpeg, jpg, png, or webp.',
            'cover_image.max'   => 'Cover image size cannot exceed 5MB.',
            'images.*.max'      => 'Each image size cannot exceed 5MB.',
        ];
    }
}
