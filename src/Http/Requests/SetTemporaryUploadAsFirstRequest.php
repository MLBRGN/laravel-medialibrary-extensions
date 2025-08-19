<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Mlbrgn\MediaLibraryExtensions\Rules\AtLeastOneCollection;

/**
 * Handles the validation rules and authorization for the SetAsFirstRequest.
 * This class ensures that the necessary input parameters are present and valid.
 */
class SetTemporaryUploadAsFirstRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target_media_collection' => ['required', 'string'],
            'medium_id' => 'required|string',
            'initiator_id' => ['required', 'string'],

            'image_collection' => 'required_without_all:video_collection,audio_collection,document_collection,youtube_collection',
            'video_collection' => 'required_without_all:image_collection,audio_collection,document_collection,youtube_collection',
            'audio_collection' => 'required_without_all:image_collection,video_collection,document_collection,youtube_collection',
            'document_collection' => 'required_without_all:image_collection,video_collection,audio_collection,youtube_collection',
            'youtube_collection' => 'required_without_all:image_collection,video_collection,audio_collection,document_collection',
        ];
    }
}
