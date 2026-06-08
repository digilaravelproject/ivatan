<?php

namespace App\Http\Requests\Api\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SwitchProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to_profile_type' => [
                'required',
                'string',
                \Illuminate\Validation\Rule::in(['personal', 'employer', 'seller', 'ecommerce', 'music', 'creator']),
            ],
            'notes' => 'nullable|string|max:2000',
            'profile_sub_type' => [
                'nullable',
                'string',
                \Illuminate\Validation\Rule::in(['product', 'products', 'service', 'services', 'both']),
                function ($attribute, $value, $fail) {
                    $profileType = $this->input('to_profile_type');
                    if ($profileType !== 'seller' && $profileType !== 'ecommerce') {
                        $fail('The profile sub type must be null unless the profile type is seller or ecommerce.');
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'to_profile_type.in' => 'Invalid profile type. Allowed: personal, employer, seller, music, creator.',
        ];
    }
}
