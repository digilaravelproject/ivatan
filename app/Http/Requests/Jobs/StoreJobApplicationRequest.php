<?php

namespace App\Http\Requests\Jobs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only non-employers can apply
        return Auth::check() && !Auth::user()->is_employer;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'cover_message' => 'nullable|string|max:2000',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10 MB
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'cover_message.string' => 'Cover message must be a valid text.',
            'cover_message.max' => 'Cover message may not exceed 2000 characters.',
            'resume.file' => 'The resume must be a valid file.',
            'resume.required' => 'Resume is required.',
            'resume.mimes' => 'Resume must be a PDF or Word document (doc, docx).',
            'resume.max' => 'Resume size may not exceed 10 MB.',
        ];
    }

    /**
     * Attribute names for better error messages
     */
    public function attributes(): array
    {
        return [
            'cover_message' => 'Cover Message',
            'resume' => 'Resume',
        ];
    }
}
