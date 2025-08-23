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
        $uploadFieldName = config('media-library-extensions.upload_field_name_youtube');

        // NOTE: mimetypes checks for mimetype in file, mimes only checks extension
        return [
            'temporary_upload' => ['required', 'string', Rule::in(['true', 'false'])],
            'model_type' => ['required', 'string'],
            'model_id' => ['required_if:temporary_upload,false'],
            'youtube_collection' => ['required', 'string'],
            'image_collection' => ['nullable', 'string'],
            'document_collection' => ['nullable', 'string'],
            'video_collection' => ['nullable', 'string'],
            'audio_collection' => ['nullable', 'string'],
            $uploadFieldName => ['nullable', 'url', new YouTubeUrl()],
            'initiator_id' => ['required', 'string'],
            'multiple' => ['required', Rule::in(['true', 'false'])],
        ];
    }
}
