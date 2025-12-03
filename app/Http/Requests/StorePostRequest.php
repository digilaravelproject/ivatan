<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePostRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $type = $this->input('type');

        // ✅ 1. Define "All Formats" lists (Instagram Style)
        // Images: Added webp, heic (iPhone), heif
        $allImageMimes = 'image/jpeg,image/png,image/jpg,image/webp,image/heic,image/heif';

        // Videos: Added avi, wmv, mkv, webm (Android/Web), quicktime (iPhone .mov)
        $allVideoMimes = 'video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm';

        // Combined for mixed types (Posts/Carousels)
        $mixedMimes = "$allImageMimes,$allVideoMimes";

        // ✅ 2. Max File Size (50MB = 51200 KB) - Adjust as needed
        $maxSize = 'max:51200';

        $rules = [
            'type' => 'required|in:post,video,reel,carousel',
            'caption' => 'nullable|string|max:2200', // Instagram caption limit
            'visibility' => 'required|in:public,private,friends',
        ];

        // ✅ 3. Dynamic Rules based on Post Type
        switch ($type) {
            case 'post':
                // Post: Single File (Image OR Video)
                $rules['media'] = 'required|array|size:1';
                $rules['media.*'] = "file|$maxSize|mimetypes:$mixedMimes";
                break;

            case 'video':
            case 'reel':
                // Reel/Video: Single File (Video ONLY)
                $rules['media'] = 'required|array|size:1';
                $rules['media.*'] = "file|$maxSize|mimetypes:$allVideoMimes";
                break;

            case 'carousel':
                // Carousel: Multiple Files (Mixed Images & Videos allowed like Instagram)
                $rules['media'] = 'required|array|min:2|max:10';
                $rules['media.*'] = "file|$maxSize|mimetypes:$mixedMimes";
                break;

            default:
                // Fallback
                $rules['media'] = 'required|array';
                $rules['media.*'] = "file|$maxSize|mimetypes:$mixedMimes";
                break;
        }

        return $rules;
    }

    /**
     * Custom error messages for better frontend handling.
     */
    public function messages(): array
    {
        return [
            'media.required' => 'Please upload at least one file.',
            'media.max' => 'You can upload a maximum of 10 files for a carousel.',
            'media.*.mimetypes' => 'The file format is not supported. Please use JPG, PNG, MP4, or MOV.',
            'media.*.max' => 'The file size must not exceed 50MB.',
        ];
    }
}
