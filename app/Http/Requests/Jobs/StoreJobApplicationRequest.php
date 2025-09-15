<?php

namespace App\Http\Requests\Jobs;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && !auth()->user()->is_employer;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'job_id' => 'required|exists:user_job_posts,id',
            'cover_message' => 'nullable|string|max:2000',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10 MB
        ];
    }
}
