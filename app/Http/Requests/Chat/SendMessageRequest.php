<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
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
            // 'chat_id' => 'required|integer|exists:user_chats,id',
            'content' => 'nullable|string',
            'message_type' => [
                'required_with:content',
                Rule::in(['text', 'image', 'file']),
            ],
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,pdf,docx,zip|max:20480', // 20 MB, restrict to certain file types
            'reply_to_message_id' => 'nullable|integer|exists:user_chat_messages,id',
        ];
    }

    /**
     * Custom validation to ensure either content or attachment is provided.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hasContent = $this->filled('content') || $this->hasFile('attachment');
            if (! $hasContent) {
                $validator->errors()->add('content', 'Either content or attachment is required.');
            }
        });
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'chat_id.required' => 'Chat ID is required to send a message.',
            'chat_id.exists' => 'The provided chat does not exist.',
            'content.string' => 'The content must be a valid string.',
            'message_type.required_with' => 'The message type is required when content is provided.',
            'message_type.in' => 'The message type must be either text, image, or file.',
            'attachment.mimes' => 'The attachment must be a file of type: jpeg, png, jpg, pdf, docx, zip.',
            'attachment.max' => 'The attachment must not be greater than 20MB.',
            'reply_to_message_id.exists' => 'The reply message does not exist.',
        ];
    }
}
