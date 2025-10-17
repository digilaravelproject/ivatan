<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;


class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() ?? auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
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
            'interests' => 'nullable|array',
            'interests.*' => 'string|max:255',
            'profile_photo' => 'sometimes|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
}
