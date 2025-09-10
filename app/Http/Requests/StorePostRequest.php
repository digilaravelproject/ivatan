<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check(); // Only authenticated users can make this request
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $type = $this->input('type');

        // Common rules
        $rules = [
            'type' => 'required|in:post,video,reel,carousel',
            'caption' => 'nullable|string|max:2200',
            'visibility' => 'required|in:public,private,friends',
        ];

        // Dynamic media validation based on post type
        switch ($type) {
            case 'post':
                $rules['media'] = 'required|array|size:1';
                $rules['media.*'] = 'file|max:51200|mimetypes:image/jpeg,image/png,video/mp4,video/quicktime';
                break;

            case 'video':
            case 'reel':
                $rules['media'] = 'required|array|size:1';
                $rules['media.*'] = 'file|max:51200|mimetypes:video/mp4,video/quicktime';
                break;

            case 'carousel':
                $rules['media'] = 'required|array|min:2|max:10';
                $rules['media.*'] = 'file|max:51200|mimetypes:image/jpeg,image/png';
                break;

            default:
                // Optional fallback if needed
                $rules['media'] = 'required|array';
                $rules['media.*'] = 'file|max:51200|mimetypes:image/jpeg,image/png,video/mp4,video/quicktime';
                break;
        }

        return $rules;
    }
}



// namespace App\Http\Requests;

// use Illuminate\Foundation\Http\FormRequest;

// class StorePostRequest extends FormRequest
// {
//     /**
//      * Determine if the user is authorized to make this request.
//      */
//     public function authorize(): bool
//     {
//         return auth()->check();
//     }

//     /**
//      * Get the validation rules that apply to the request.
//      *
//      * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
//      */
//     public function rules(): array
//     {
//         $rules = [
//             'type' => 'required|in:post,video,reel,carousel',
//             'caption' => 'nullable|string|max:2200',
//             'visibility' => 'required|in:public,private,friends',
//             'media' => 'required|array',
//             'media.*' => 'file|max:51200|mimetypes:image/jpeg,image/png,video/mp4,video/quicktime',
//         ];
//         if ($this->input('type')=== 'post') {
//             $rules['media']= 'required|array|size:1';
//             # code...
//         } elseif ($this->input('type')=== 'video') {
//           $rules['media']= 'required|array|size:1';
//           $rules['media.*'] .= '|mimetypes:video/mp4,video/quicktime';
//         }
//          elseif ($this->input('type')=== 'reel') {
//           $rules['media']= 'required|array|size:1';
//           $rules['media.*'] .= '|mimetypes:video/mp4,video/quicktime';
//         }
//          elseif ($this->input('type')=== 'carousel') {
//           $rules['media']= 'required|array|min:2|max:10';
//         }





//         return $rules;
//     }
// }
