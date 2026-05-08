<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Rule;
use Mlbrgn\MediaLibraryExtensions\Rules\YouTubeUrl;

class StoreYouTubeVideoRequest extends StoreRequest
{

    public function rules(): array
    {
        $uploadFieldName = config('media-library-extensions.upload_field_name_youtube');

        // NOTE: mimetypes checks for mimetype in file, mimes only checks extension
        return [
            'temporary_upload_mode' => ['required', 'string', Rule::in(['true', 'false'])],
            'model_type' => ['required', 'string'],
            'model_id' => ['required_if:temporary_upload_mode,false'],
            'collections' => ['required', 'array', 'min:1'],
            'collections.*' => ['nullable', 'string'],
            $uploadFieldName => ['nullable', 'url', new YouTubeUrl],
            'initiator_id' => ['required', 'string'],
            'media_manager_id' => ['required', 'string'],
            'multiple' => ['required', Rule::in(['true', 'false'])],
        ];
    }
}
