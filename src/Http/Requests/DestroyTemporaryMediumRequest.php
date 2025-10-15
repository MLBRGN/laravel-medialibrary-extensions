<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

/**
 * Handles authorization and validation rules for media manager temporary upload destroy requests.
 */
class DestroyTemporaryMediumRequest extends MediaManagerRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'initiator_id' => ['required', 'string'],
            'media_manager_id' => ['required', 'string'],
            'image_collection' => 'required_without_all:video_collection,audio_collection,document_collection,youtube_collection',
            'video_collection' => 'required_without_all:image_collection,audio_collection,document_collection,youtube_collection',
            'audio_collection' => 'required_without_all:image_collection,video_collection,document_collection,youtube_collection',
            'document_collection' => 'required_without_all:image_collection,video_collection,audio_collection,youtube_collection',
            'youtube_collection' => 'required_without_all:image_collection,video_collection,audio_collection,document_collection',
        ];
    }
}
