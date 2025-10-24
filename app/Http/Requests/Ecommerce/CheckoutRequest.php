<?php

namespace App\Http\Requests\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Payment method validation
            'payment_method' => 'required|string|in:cod,razorpay,stripe',

            // Shipping address validation
            'shipping_address' => 'required|array',
            'shipping_address.name' => 'required|string|max:255',
            'shipping_address.phone' => 'required|string|max:30|regex:/^[7-9][0-9]{9}$/',  // Phone number format validation (India)
            'shipping_address.address_line1' => 'required|string|max:255',
            'shipping_address.address_line2' => 'nullable|string|max:255',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'required|string|max:100',
            'shipping_address.country' => 'nullable|string|max:100  ',  // Optional: Ensure country exists in countries table
            'shipping_address.postal_code' => 'required|string|max:20',

            // Optional fields
            'notes' => 'nullable|string|max:2000',

            // Optional: Coupon validation if applicable in future
            'coupon_code' => 'nullable|string|exists:coupons,code',  // Example if you have a coupon system
        ];
    }

    /**
     * Get the custom validation messages for the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Payment method validation
            'payment_method.required' => 'Please select a valid payment method.',
            'payment_method.in' => 'The selected payment method is invalid. Available options are: COD, Razorpay, Stripe.',

            // Shipping address validation
            'shipping_address.required' => 'Shipping address is required.',
            'shipping_address.name.required' => 'Name is required.',
            'shipping_address.name.max' => 'Name should not exceed 255 characters.',
            'shipping_address.phone.required' => 'Phone number is required.',
            'shipping_address.phone.regex' => 'Phone number is invalid. Please provide a valid phone number.',
            'shipping_address.phone.max' => 'Phone number should not exceed 30 characters.',
            'shipping_address.address_line1.required' => 'Address Line 1 is required.',
            'shipping_address.address_line1.max' => 'Address Line 1 should not exceed 255 characters.',
            'shipping_address.address_line2.max' => 'Address Line 2 should not exceed 255 characters.',
            'shipping_address.city.required' => 'City is required.',
            'shipping_address.city.max' => 'City name should not exceed 100 characters.',
            'shipping_address.state.required' => 'State is required.',
            'shipping_address.state.max' => 'State name should not exceed 100 characters.',
            'shipping_address.country.max' => 'Country name should not exceed 100 characters.',
            'shipping_address.country.exists' => 'The selected country is invalid.',
            'shipping_address.postal_code.required' => 'Postal code is required.',
            'shipping_address.postal_code.max' => 'Postal code should not exceed 20 characters.',

            // Optional fields
            'notes.max' => 'Notes should not exceed 2000 characters.',

            // Optional: Coupon validation if applicable in future
            'coupon_code.exists' => 'The coupon code you entered is invalid or expired.',
        ];
    }
}
