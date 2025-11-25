<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Interest; // Model import karna mat bhoolna

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|unique:users',
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:8',
            'date_of_birth' => 'required|date',
            'occupation' => 'nullable|string|max:255',

            // Interests Validation Logic
            'interests' => 'nullable|array',
            'interests.*' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Check 1: Agar Number hai, to ID check karo
                    if (is_numeric($value)) {
                        if (!Interest::where('id', $value)->exists()) {
                            $fail("The selected interest ID ($value) is invalid.");
                        }
                    }
                    // Check 2: Agar String hai, to Name check karo
                    elseif (is_string($value)) {
                        if (!Interest::where('name', $value)->exists()) {
                            $fail("The interest '$value' does not exist. Admin must create it first.");
                        }
                    }
                    // Check 3: Agar kuch aur hai
                    else {
                        $fail("Invalid interest format.");
                    }
                },
            ],
        ];
    }
}
