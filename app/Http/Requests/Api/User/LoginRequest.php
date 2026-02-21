<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @property string|null $email
     * @property string|null $phone
     * @property string|null $username
     * @property string $password
     */
    public function rules(): array
    {
        return [
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'username' => 'nullable|string',
            'password' => 'required|string|min:8',
        ];
    }

    /**
     * Add custom validator to ensure at least one of email, phone, or username is present.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (
                !$this->filled('email') &&
                !$this->filled('phone') &&
                !$this->filled('username')
            ) {
                $validator->errors()->add(
                    'email_or_phone_or_username',
                    'The email or phone or username field is required.'
                );
            }
        });
    }
    public function identifier(): string
    {
        return $this->email ?? $this->phone ?? $this->username;
    }
}
