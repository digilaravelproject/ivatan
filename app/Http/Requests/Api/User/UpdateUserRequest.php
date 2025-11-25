<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Interest; // Model import karna zaroori hai validation ke liye

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
        // Current User ki ID nikali taaki unique check mein khud ko ignore kare
        $userId = $this->user()->id;

        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $userId,
            'phone' => 'sometimes|string|unique:users,phone,' . $userId,
            'username' => 'sometimes|string|max:50|unique:users,username,' . $userId,
            'password' => 'sometimes|string|min:8',
            'date_of_birth' => 'sometimes|date',
            'occupation' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'profile_photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // 2MB Max

            // ✅ Interests Logic: Accepts Mixed (ID or Name)
            'interests' => 'nullable|array',
            'interests.*' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Case 1: Agar ID bheji hai (Number)
                    if (is_numeric($value)) {
                        if (!Interest::where('id', $value)->exists()) {
                            $fail("The selected interest ID ($value) is invalid.");
                        }
                    }
                    // Case 2: Agar Name bheja hai (String)
                    elseif (is_string($value)) {
                        if (!Interest::where('name', $value)->exists()) {
                            $fail("The interest '$value' does not exist. Admin must create it first.");
                        }
                    }
                    // Case 3: Invalid format
                    else {
                        $fail("Invalid interest format.");
                    }
                },
            ],
        ];
    }
}
