<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MediaManagerDestroyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'medium' => [
                'nullable',
                'mimes:'.implode(',', config('media.allowed_mimes.image')),
                'max:'.config('media.max_upload_sizes.image'),
            ],
        ];
    }
}
