<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreAdRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
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
            'description' => 'nullable|string',
            'ad_package_id' => 'required|exists:ad_packages,id',
            'interest_id' => 'required|array|min:1',       // now array
            'interest_id.*' => 'exists:interests,id',     // each id must exist
            'start_type' => 'nullable|in:immediate,scheduled',
            'start_at' => 'nullable|date|after_or_equal:today',
            'media' => 'nullable|array',
            'media.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,mov|max:10240',
        ];
    }
}
