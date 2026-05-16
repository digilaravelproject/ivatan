<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class StoreSellerFinancialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->is_seller;
    }

    public function rules(): array
    {
        return [
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|min:9|max:18|confirmed',
            'ifsc_code' => ['required', 'string', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'account_type' => 'required|in:savings,current,overdraft',
        ];
    }

    public function messages(): array
    {
        return [
            'ifsc_code.regex' => 'Invalid IFSC code format (e.g., SBIN0123456)',
        ];
    }
}
