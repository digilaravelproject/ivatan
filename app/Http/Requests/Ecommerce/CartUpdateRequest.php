<?php

namespace App\Http\Requests\Ecommerce;

use App\Models\Ecommerce\UserCart;
use App\Models\Ecommerce\UserProduct;
use Illuminate\Foundation\Http\FormRequest;


/**
 * Class CartUpdateRequest
 *
 * Handles the validation for cart update requests.
 * Ensures that the user is authorized to update the cart item and validates the quantity of the product.
 *
 * @package App\Http\Requests\Ecommerce
 */
class CartUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Allow any user to access this request
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Validate the quantity for products in the cart
            'quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    if ($this->has('item_id') && $this->has('item_type') && $this->item_type === 'user_products') {
                        /** @var \App\Models\Ecommerce\UserProduct|null $product */
                        $product = UserProduct::find($this->item_id);
                        // Ensure that the requested quantity doesn't exceed the available stock
                        if ($product && $value > $product->stock) {
                            return $fail("Sorry, only {$product->stock} units are available.");
                        }
                    }
                },
            ],
        ];
    }

    /**
     * Get all the input data from the request, including dynamic properties like item_type and item_id.
     *
     * @param array|null $keys
     * @return array
     */
    public function all($keys = null): array
    {
        return parent::all($keys);  // Use the parent method with the optional $keys parameter
    }
}
