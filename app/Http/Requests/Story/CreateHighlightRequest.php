<?php

namespace App\Http\Requests\Story;

use Illuminate\Foundation\Http\FormRequest;

class CreateHighlightRequest extends FormRequest
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
            'title' => 'required|string|max:100',
            'cover_media' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
