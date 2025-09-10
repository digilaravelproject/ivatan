<?php

namespace App\Http\Requests\Story;

use Illuminate\Foundation\Http\FormRequest;

class StoreStoryRequest extends FormRequest
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
           'caption' => 'nullable|string|max:1000',          // text overlay / caption
            'meta' => 'nullable|array',                       // e.g. {"text_style": {...}, "stickers": [...]}
            'media' => 'required|file|max:51200|mimetypes:image/jpeg,image/png,video/mp4,video/quicktime',
            // optional: expires_at override (not recommended)
            'expires_at' => 'nullable|date|after:now',
        ];
    }
}
