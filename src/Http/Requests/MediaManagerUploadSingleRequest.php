<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

/**
 * Handles the validation rules for uploading a single media file.
 */
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
        $uploadFieldNameSingle = config('media-library-extensions.upload_field_name_single');

        // NOTE: mimetypes checks for mimetype in file, mimes only checks extension
        return [
            'model_type' => ['required', 'string'],
            'model_id' => ['required', 'string'],
//            'collection_name' => ['required', 'string'],
            'image_collection' => ['required', 'string'],
            'document_collection' => ['nullable', 'string'],
            $uploadFieldNameSingle => [
                'nullable',
                'mimetypes:'.implode(',', Arr::flatten(config('media-library-extensions.allowed_mimetypes'))),
                'max:'.config('media-library-extensions.max_upload_size'),
            ],
            'initiator_id' => ['required', 'string'],
        ];
    }
}
