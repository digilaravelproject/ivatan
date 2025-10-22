<?php

namespace App\Http\Requests\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreUserServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Ensure the user is authenticated and a seller
        return Auth::check() && Auth::user()?->is_seller;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Title is required, should be a string, and max length of 255 characters
            'title' => 'required|string|max:255|unique:user_services,title',

            // Description is optional and should be a string
            'description' => 'nullable|string',

            // Price is required, numeric, and must be at least 0
            'price' => 'required|numeric|min:0',

            // Cover image is optional, but if present, it must be an image with specific mime types
            'cover_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',

            // Images array is optional, but each item must follow the same rules as cover_image
            'images.*' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ];
    }

    /**
     * Get custom error messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The service title is required.',
            'title.string' => 'The service title must be a valid string.',
            'title.max' => 'The service title may not be greater than 255 characters.',
            'title.unique' => 'A service with this title already exists. Please choose a different title.',
            'description.string' => 'The description must be a valid string.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price must be at least 0.',
            'cover_image.image' => 'The cover image must be an image file.',
            'cover_image.mimes' => 'The cover image must be a jpeg, jpg, png, or webp image.',
            'cover_image.max' => 'The cover image may not be greater than 5MB.',
            'images.*.image' => 'Each image must be an image file.',
            'images.*.mimes' => 'Each image must be a jpeg, jpg, png, or webp image.',
            'images.*.max' => 'Each image may not be greater than 5MB.',
        ];
    }
}
