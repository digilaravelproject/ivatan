<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Interest;

class UpdateUserRequest extends FormRequest
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
     */
    public function rules(): array
    {
        // Current User ID to ignore unique checks for self
        $userId = $this->user()->id;

        return [
            // --- Basic Identity ---
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $userId,
            'phone' => 'sometimes|string|unique:users,phone,' . $userId,
            'username' => 'sometimes|string|max:50|unique:users,username,' . $userId,

            // --- Security ---
            'password' => 'sometimes|string|min:8',

            // --- Personal Details ---
            'date_of_birth' => 'sometimes|date',
            'occupation' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'gender' => 'nullable|in:male,female,other,prefer_not_to_say',
            'language_preference' => 'nullable|string|max:10', // e.g., 'en', 'hi', 'es'

            // --- Privacy Settings ---
            'account_privacy' => 'nullable|in:public,private',
            'messaging_privacy' => 'nullable|in:everyone,followers,none',

            // --- JSON Settings (Frontend sends JSON/Array) ---
            'settings' => 'nullable|array',
            'email_notification_preferences' => 'nullable|array',

            // --- Media ---
            'profile_photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB Max

            // --- Interests (Mixed ID or Name) ---
            'interests' => 'nullable|array',
            'interests.*' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Case 1: ID (Numeric)
                    if (is_numeric($value)) {
                        if (!Interest::where('id', $value)->exists()) {
                            $fail("The selected interest ID ($value) is invalid.");
                        }
                    }
                    // Case 2: Name (String)
                    elseif (is_string($value)) {
                        if (!Interest::where('name', $value)->exists()) {
                            $fail("The interest '$value' does not exist. Admin must create it first.");
                        }
                    }
                    // Case 3: Invalid
                    else {
                        $fail("Invalid interest format.");
                    }
                },
            ],
        ];
    }
}
