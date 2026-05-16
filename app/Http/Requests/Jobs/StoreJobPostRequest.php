<?php

namespace App\Http\Requests\Jobs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

/**
 * Class StoreJobPostRequest
 *
 * Handles validation and authorization for creating/updating job posts.
 *
 * @property string $title
 * @property string|null $company_name
 * @property string|null $company_website
 * @property UploadedFile|null $company_logo
 * @property string $description
 * @property string|null $responsibilities
 * @property string|null $requirements
 * @property string|null $location
 * @property string|null $country
 * @property string $employment_type
 * @property float|null $salary_min
 * @property float|null $salary_max
 * @property string|null $currency
 * @property bool $is_remote
 * @property string|null $status
 *
 * @method UploadedFile|null file(string $key = null, $default = null)
 * @method bool hasFile(string $key)
 */
class StoreJobPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->is_employer;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'             => 'required|string|max:255',
            'company_name'      => 'nullable|string|max:255',
            'company_website'   => 'nullable|url|max:255',
            'company_logo'      => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
            'description'       => 'required|string',
            'responsibilities'  => 'nullable|string',
            'requirements'      => 'nullable|string',
            'location'          => 'nullable|string|max:255',
            'country'           => 'nullable|string|max:100',
            'employment_type'   => 'required|in:full_time,part_time,contract,internship,freelance',
            'salary_min'        => 'nullable|numeric|min:0',
            'salary_max'        => 'nullable|numeric|min:0',
            'currency'          => 'nullable|string|max:10',
            'is_remote'         => 'boolean',
            'is_urgent'         => 'boolean',
            'status'            => 'nullable|in:draft,published,closed',
        ];
    }

    /**
     * Get custom validation messages for specific rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required'            => 'The job title is required.',
            'title.string'              => 'The job title must be a valid string.',
            'title.max'                 => 'The job title may not be greater than 255 characters.',

            'company_name.string'       => 'The company name must be a valid string.',
            'company_name.max'          => 'The company name may not exceed 255 characters.',

            'company_website.url'       => 'Please enter a valid company website URL.',
            'company_website.max'       => 'The company website URL may not exceed 255 characters.',

            'company_logo.image'        => 'The company logo must be an image file.',
            'company_logo.mimes'        => 'The company logo must be a file of type: jpeg, jpg, png, webp.',
            'company_logo.max'          => 'The company logo size may not exceed 5MB.',

            'description.required'      => 'Please provide a detailed job description.',
            'description.string'        => 'The job description must be valid text.',

            'employment_type.required'  => 'Please select an employment type.',
            'employment_type.in'        => 'Invalid employment type selected.',

            'salary_min.numeric'        => 'Minimum salary must be a number.',
            'salary_min.min'            => 'Minimum salary cannot be negative.',

            'salary_max.numeric'        => 'Maximum salary must be a number.',
            'salary_max.min'            => 'Maximum salary cannot be negative.',

            'currency.string'           => 'Currency must be a valid string.',
            'currency.max'              => 'Currency code may not exceed 10 characters.',

            'is_remote.boolean'         => 'The remote option must be true or false.',
            'is_urgent.boolean'         => 'The urgent option must be true or false.',

            'status.in'                 => 'Invalid status value. Allowed: draft, published, closed.',
        ];
    }

    /**
     * Customize the validation error messages attributes (optional).
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title'           => 'Job Title',
            'company_name'    => 'Company Name',
            'company_website' => 'Company Website',
            'company_logo'    => 'Company Logo',
            'employment_type' => 'Employment Type',
        ];
    }
}
