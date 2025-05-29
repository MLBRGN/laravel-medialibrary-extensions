<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

/**
 * Handle the validation and authorization for uploading multiple media files.
 */
class MediaManagerUploadMultipleRequest extends FormRequest
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
        $uploadFieldName = config('media-library-extensions.upload_field_name');

        // NOTE: mimes only tests on file extension, so use mimetypes instead for it's safer
        return [
            'model_type' => ['required', 'string'],
            'model_id' => ['required', 'string'],
            'collection_name' => ['required', 'string'],
            $uploadFieldName => 'nullable|array',
            $uploadFieldName.'.media.*' => [
                'nullable',
                'mimetypes:'.implode(',', Arr::flatten(config('media-library-extensions.allowed_mimetypes'))),
                'max:'.config('media-library-extensions.max_upload_size'),
            ],
            'target_id' => ['required', 'string'],
        ];
    }
}
