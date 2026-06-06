<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ApproveSwitchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'status' => 'required|string|in:approved,rejected',
            'admin_notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status must be either "approved" or "rejected".',
        ];
    }
}
