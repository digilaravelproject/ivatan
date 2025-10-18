<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class CreatePrivateChatRequest extends FormRequest
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
            'other_user_id' => [
                'required',
                'integer',
                'exists:users,id',
                'different:' . auth()->id(),
                // Rule::notIn(BlockedUser::where('user_id', auth()->id())->pluck('blocked_user_id')->toArray()),
            ],
        ];
    }
    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'other_user_id.required' => 'The user ID of the person you want to chat with is required.',
            'other_user_id.integer' => 'The user ID must be a valid integer.',
            'other_user_id.exists' => 'The user you are trying to chat with does not exist.',
            'other_user_id.different' => 'You cannot start a chat with yourself.',
            'other_user_id.not_in' => 'You cannot start a chat with someone you have blocked.',
        ];
    }
}
