<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Rule;
use Mlbrgn\MediaLibraryExtensions\Rules\YouTubeUrl;

/**
 * Handle the validation and authorization for uploading a YouTube video
 */
class StoreYouTubeVideoRequest extends MediaManagerRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $uploadFieldName = config('media-library-extensions.upload_field_name_youtube');

        // NOTE: mimetypes checks for mimetype in file, mimes only checks extension
        return [
            'temporary_upload_mode' => ['required', 'string', Rule::in(['true', 'false'])],
            'model_type' => ['required', 'string'],
            'model_id' => ['required_if:temporary_upload_mode,false'],
            'collections' => ['required', 'array'],
            'collections.*' => ['nullable', 'string'],
//            'youtube_collection' => ['required', 'string'],
//            'image_collection' => ['nullable', 'string'],
//            'document_collection' => ['nullable', 'string'],
//            'video_collection' => ['nullable', 'string'],
//            'audio_collection' => ['nullable', 'string'],
            $uploadFieldName => ['nullable', 'url', new YouTubeUrl],
            'initiator_id' => ['required', 'string'],
            'media_manager_id' => ['required', 'string'],
            'multiple' => ['required', Rule::in(['true', 'false'])],
        ];
    }
}
