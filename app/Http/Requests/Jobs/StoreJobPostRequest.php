<?php

namespace App\Http\Requests\Jobs;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_employer;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'company_website' => 'nullable|url|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'description' => 'required|string',
            'responsibilities' => 'nullable|string',
            'requirements' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'employment_type' => 'required|in:full_time,part_time,contract,internship,freelance',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'is_remote' => 'boolean',
            'status' => 'nullable|in:draft,published,closed',
        ];
    }
}
