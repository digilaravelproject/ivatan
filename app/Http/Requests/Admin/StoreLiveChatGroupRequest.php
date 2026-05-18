<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLiveChatGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $groupId = $this->route('live_chat_group');
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'chat_mode' => ['required', Rule::in(['admin_only', 'everyone'])],
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'chat_mode.in' => 'Chat mode must be admin_only or everyone.',
        ];
    }
}
