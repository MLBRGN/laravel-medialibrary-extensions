<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class MediaManagerUploadSingleRequest extends FormRequest
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
            'model_type' => ['required', 'string'],
            'model_id' => ['required', 'string'],
            'collection_name' => ['required', 'string'],
            'medium' => [
                'nullable',
                'mimes:'.implode(',', config('media.allowed_mimes.image')),
                'max:'.config('media.max_upload_sizes.image'),
            ],
        ];
    }
}
