<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       return [
            'chat_id' => 'required|integer|exists:user_chats,id',
            'content' => 'nullable|string',
            'message_type' => 'required_with:content|in:text,image,file',
            'attachment' => 'nullable|file|max:20480', // 20 MB, adjust as needed
            'reply_to_message_id' => 'nullable|integer|exists:user_chat_messages,id',
        ];
    }
     public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hasContent = $this->filled('content') || $this->hasFile('attachment');
            if (! $hasContent) {
                $validator->errors()->add('content', 'Either content or attachment is required.');
            }
        });
    }
}
