<?php

namespace App\Http\Requests\Api\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'string',
                Rule::in(['employer', 'ecommerce', 'seller', 'music', 'creator']),
            ],
            'profile_sub_type' => 'required_if:type,ecommerce,seller|string|in:product,service,both',
            'business_name' => 'nullable|string|max:255',
            'business_description' => 'nullable|string|max:2000',
            'business_email' => 'nullable|email|max:255',
            'business_phone' => 'nullable|string|max:20',
            'company_name' => 'required_if:type,employer|string|max:255',
            'industry' => 'nullable|string|max:255',
            'company_size' => 'nullable|string|max:50',
            'company_website' => 'nullable|url|max:255',
            'artist_name' => 'nullable|string|max:255',
            'stage_name' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:100',
            'channel_name' => 'required_if:type,creator|string|max:255',
            'content_category' => 'nullable|string|max:100',
            'platform' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'profile_sub_type.required_if' => 'Profile sub type is required when creating an ecommerce profile.',
            'company_name.required_if' => 'Company name is required when creating an employer profile.',
            'channel_name.required_if' => 'Channel name is required when creating a creator profile.',
        ];
    }
}
