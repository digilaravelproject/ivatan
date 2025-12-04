<?php

namespace App\Http\Requests\Story;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'caption' => 'nullable|string|max:1000',
            'meta' => 'nullable|array',
            'media' => 'required|file|mimes:jpeg,png,jpg,mp4,mov,qt|max:51200', // 50MB
            'expires_at' => 'nullable|date|after:now',
        ];
    }
}
