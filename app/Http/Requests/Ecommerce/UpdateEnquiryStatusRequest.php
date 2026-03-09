<?php

namespace App\Http\Requests\Ecommerce;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEnquiryStatusRequest extends FormRequest
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
            'status' => 'required|in:pending,replied,closed',
            'reply_message' => 'nullable|string|max:5000',
        ];
    }
}
