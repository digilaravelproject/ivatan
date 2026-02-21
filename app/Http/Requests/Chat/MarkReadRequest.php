<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MarkReadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check(); // Ensure only logged in users can call
    }

    public function rules(): array
    {
        return [
            // Chat ID URI parameter se aata hai, body se sirf message ID chahiye
            'last_read_message_id' => 'required|integer|exists:user_chat_messages,id',
        ];
    }
}
