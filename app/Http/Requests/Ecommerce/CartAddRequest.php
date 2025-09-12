<?php

namespace App\Http\Requests\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;

class CartAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'item_type' => 'required|string|in:user_products,user_services',
            'item_id'   => 'required|integer',
            'seller_id' => 'required|integer',
            'price'     => 'required|numeric|min:0',
            'quantity'  => 'nullable|integer|min:1',
        ];
    }
}
