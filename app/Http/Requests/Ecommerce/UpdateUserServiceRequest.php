<?php

namespace App\Http\Requests\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class UpdateUserServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|nullable|numeric|min:0',
            'cover_image' => 'sometimes|nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'images.*' => 'sometimes|nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'remove_image_ids' => 'sometimes|nullable|array',
            'remove_image_ids.*' => 'sometimes|nullable|integer|exists:user_service_images,id',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The service title is required when provided.',
            'description.string' => 'The description must be a valid string.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price must be a positive value.',
            'cover_image.image' => 'The cover image must be a valid image file (jpeg, jpg, png, or webp).',
            'cover_image.mimes' => 'The cover image must be in jpeg, jpg, png, or webp format.',
            'cover_image.max' => 'The cover image must not exceed 5MB.',
            'images.*.image' => 'Each image must be a valid image file (jpeg, jpg, png, or webp).',
            'images.*.mimes' => 'Each image must be in jpeg, jpg, png, or webp format.',
            'images.*.max' => 'Each image must not exceed 5MB.',
            'remove_image_ids.array' => 'Remove image IDs must be an array.',
            'remove_image_ids.*.integer' => 'Each remove image ID must be an integer.',
            'remove_image_ids.*.exists' => 'The remove image ID must exist in the database.',
            'empty' => 'You must provide at least one of the fields or images to update.',
        ];
    }

    /**
     * Add custom validation after the initial validation.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            /** @var \Illuminate\Http\Request $request */
            $request = $this;

            // Check if at least one field is filled or a file is uploaded
            $fieldsToCheck = [
                'title',
                'description',
                'price',
                'cover_image',
                'images',
                'remove_image_ids'
            ];

            // Using `collect` to loop through the fields and check if any of them are filled or a file is uploaded
            $hasAtLeastOneField = collect($fieldsToCheck)->contains(function ($field) use ($request) {
                return $request->filled($field) || $request->hasFile($field);
            });

            // Add custom error if no fields are filled
            if (!$hasAtLeastOneField) {
                $validator->errors()->add('empty', 'You must provide at least one of the fields or images to update.');
            }
        });
    }
}
