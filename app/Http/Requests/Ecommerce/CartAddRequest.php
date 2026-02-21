<?php

namespace App\Http\Requests\Ecommerce;

use App\Models\Ecommerce\UserProduct;
use App\Models\Ecommerce\UserService;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CartAddRequest
 *
 * @property string $item_type
 * @property int $item_id
 * @property int|null $quantity
 *
 * @package App\Http\Requests\Ecommerce
 */
class CartAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow any user to access this request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string
     */
    public function rules(): array
    {
        $rules = [
            'item_type' => 'required|string|in:user_products,user_services', // Ensure item_type is valid
            'item_id'   => 'required|integer', // We will check the item existence later in a custom validation
        ];

        // Apply quantity validation only for user_products
        if ($this->item_type === 'user_products') {
            $rules['quantity'] = 'required|integer|min:1'; // Ensure quantity is a valid integer and at least 1
        } else {
            // For user_services, quantity is optional and can be null
            $rules['quantity'] = 'nullable|integer|min:1'; // This allows quantity to be null or a valid integer if provided
        }

        return $rules;
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'item_type.required' => 'The item type is required. Please specify if it is a product or service.',
            'item_type.string' => 'The item type must be a valid string (e.g., user_products, user_services).',
            'item_type.in' => 'The item type must be either "user_products" or "user_services".',

            'item_id.required' => 'The item ID is required. Please provide the correct product or service ID.',
            'item_id.integer' => 'The item ID must be a valid integer.',
            'quantity.required' => 'Quantity is required. Please specify the quantity.',
            'quantity.integer' => 'Quantity must be a valid integer.',
            'quantity.min' => 'The quantity must be at least 1.',
        ];
    }

    /**
     * Perform custom validation for checking item existence based on item_type.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Check if item_type is user_products and item_id exists in user_products table
            if ($this->item_type === 'user_products') {
                if (! UserProduct::where('id', $this->item_id)->exists()) {
                    $validator->errors()->add('item_id', 'The product ID does not exist.');
                }
            }

            // Check if item_type is user_services and item_id exists in user_services table
            elseif ($this->item_type === 'user_services') {
                if (! UserService::where('id', $this->item_id)->exists()) {
                    $validator->errors()->add('item_id', 'The service ID does not exist.');
                }
            }
        });
    }
}
