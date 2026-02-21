<?php

namespace App\Http\Requests\Story;

use Illuminate\Foundation\Http\FormRequest;

class CreateHighlightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:100',
            'cover_media' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB
            'story_ids' => 'nullable|array', // Allow adding stories on create
            'story_ids.*' => 'exists:user_stories,id'
        ];
    }
}
