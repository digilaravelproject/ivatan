<?php

namespace App\Http\Requests\Api\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSellerDetailsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'seller_type' => 'sometimes|required|string|in:products,services,both',
            'business_name' => 'nullable|string|max:255',
            'business_description' => 'nullable|string|max:2000',
            'business_email' => 'nullable|email|max:255',
            'business_phone' => 'nullable|string|max:20',
            'business_address' => 'nullable|string|max:500',
        ];
    }
}
