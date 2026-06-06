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
                Rule::in(['personal', 'employer', 'seller', 'music', 'creator']),
            ],
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'to_profile_type.in' => 'Invalid profile type. Allowed: personal, employer, seller, music, creator.',
        ];
    }
}
