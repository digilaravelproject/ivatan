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
        // Only non-employers can apply
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'job_id' => $this->route('jobId'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'job_id' => 'required|exists:user_job_posts,id',
            'cover_message' => 'nullable|string|max:2000',
            // Profile Base Fields
            'resume_headline' => 'nullable|string|max:255',
            'skills_list' => 'nullable|array',
            'contact_no' => 'nullable|string|max:20',
            // Employments array
            'employments' => 'nullable|array',
            'employments.*.company_name' => 'required_with:employments|string',
            'employments.*.job_title' => 'required_with:employments|string',
            'employments.*.is_current_employment' => 'boolean',
            'employments.*.joining_date' => 'nullable|date',
            'employments.*.worked_till' => 'nullable|date',
            'employments.*.job_description' => 'nullable|string',
            // Educations array
            'educations' => 'nullable|array',
            'educations.*.university_name' => 'required_with:educations|string',
            'educations.*.course_name' => 'required_with:educations|string',
            'educations.*.course_type' => 'nullable|string',
            'educations.*.course_duration' => 'nullable|string',
            'educations.*.percentage_cgpa' => 'nullable|string',
            // Make resume nullable since profile data may suffice, but user can optionally attach
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10 MB
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'job_id.required' => 'The job ID is required.',
            'job_id.exists' => 'The selected job does not exist.',
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
