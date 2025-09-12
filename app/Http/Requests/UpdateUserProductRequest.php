<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return false;
         // controller will ensure ownership; allow if authenticated & seller
        return auth()->check() && auth()->user()->is_seller;

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
   public function rules(): array
{
    return [
        'title'            => 'sometimes|required|string|max:255',
        'description'      => 'nullable|string',
        'price'            => 'sometimes|numeric|min:0',
        'stock'            => 'sometimes|integer|min:0',
        'cover_image'      => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        'images.*'         => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        'remove_image_ids'  => 'nullable|array',
        'remove_image_ids.*'=> 'integer|exists:user_product_images,id',
    ];
}

public function withValidator($validator)
{
    $validator->after(function ($validator) {
        $data = $this->all();

        $fields = ['title', 'description', 'price', 'stock', 'cover_image', 'images', 'remove_image_ids'];

        $hasAtLeastOne = false;
        foreach ($fields as $field) {
            if ($this->filled($field) || $this->hasFile($field)) {
                $hasAtLeastOne = true;
                break;
            }
        }

        if (!$hasAtLeastOne) {
            $validator->errors()->add('empty', 'You must provide at least one field to update.');
        }
    });
}

}
