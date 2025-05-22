<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        $allowedMimeTypes = collect(config('media-library-extensions.allowed_mimes'))
            ->flatten()
            ->unique()
            ->implode(',');
        $maxImageSize = config('media-library-extensions.max_upload_sizes.image');

        // NOTE: mimes only tests on file extension, so use mimetypes instead for it's safer
        return [
            'model_type' => ['required', 'string'],
            'model_id' => ['required', 'string'],
            'collection_name' => ['required', 'string'],
            'media' => 'required|array',
            'media.*' => [
                'nullable',
                'mimetypes:'.$allowedMimeTypes,
                'max:'.$maxImageSize,
            ],
        ];
    }
}
