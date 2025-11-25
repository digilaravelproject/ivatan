<?php

namespace App\Http\Requests\Story;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth middleware controller me handle karega
    }

    public function rules(): array
    {
        return [
            'caption' => 'nullable|string|max:1000',
            'meta' => 'nullable|array',
            // Max size 50MB (51200 KB) aur specific mimes allowed
            'media' => 'required|file|mimes:jpeg,png,jpg,mp4,mov,qt|max:51200',
            'expires_at' => 'nullable|date|after:now',
        ];
    }
}
