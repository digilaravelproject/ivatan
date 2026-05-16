<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class UpdateUserProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()?->is_seller;
    }

    public function rules(): array
    {
        return [
            'title'              => 'sometimes|required|string|max:255',
            'description'        => 'nullable|string',
            'price'              => 'sometimes|numeric|min:0',
            'stock'              => 'sometimes|integer|min:0',
            'cover_image'        => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'images'             => 'nullable|array',
            'images.*'           => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            'remove_image_ids'   => 'nullable|array',
            'remove_image_ids.*' => 'integer|exists:user_product_images,id',
        ];
    }
    /**
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */


    /** @var \Illuminate\Http\Request $request */
    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            /** @var \Illuminate\Http\Request $request */
            $request = $this;

            // This checks if any field is filled or a file is uploaded
            $fieldsToCheck = [
                'title',
                'description',
                'price',
                'stock',
                'cover_image',
                'images',
                'remove_image_ids'
            ];

            $hasAtLeastOneField = collect($fieldsToCheck)->contains(function ($field) use ($request) {
                // Check if the field is filled or has a file uploaded
                return ($request->filled($field) || ($request->hasFile($field)));
            });

            if (! $hasAtLeastOneField) {
                $validator->errors()->add('empty', 'You must provide at least one field to update.');
            }
        });
    }



    /**
     * Custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required'              => 'The title is required when updating.',
            'title.max'                   => 'Title must not exceed 255 characters.',
            'price.numeric'               => 'Price must be a valid number.',
            'price.min'                   => 'Price cannot be negative.',
            'stock.integer'               => 'Stock must be a whole number.',
            'stock.min'                   => 'Stock cannot be less than zero.',
            'cover_image.image'           => 'Cover image must be a valid image file.',
            'cover_image.mimes'           => 'Cover image must be a jpeg, jpg, png, or webp file.',
            'cover_image.max'             => 'Cover image size must not exceed 5MB.',
            'images.array'                => 'Images must be an array of files.',
            'images.*.image'              => 'Each image must be a valid image file.',
            'images.*.mimes'              => 'Each image must be jpeg, jpg, png, or webp format.',
            'images.*.max'                => 'Each image size must not exceed 5MB.',
            'remove_image_ids.array'      => 'Remove image IDs must be an array.',
            'remove_image_ids.*.integer'  => 'Each remove image ID must be an integer.',
            'remove_image_ids.*.exists'   => 'One or more image IDs to remove do not exist.',
        ];
    }
}
