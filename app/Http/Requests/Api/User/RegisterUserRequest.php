<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
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
            'interests' => 'nullable|array',
            'interests.*' => 'string|max:255',
        ];
    }
}
