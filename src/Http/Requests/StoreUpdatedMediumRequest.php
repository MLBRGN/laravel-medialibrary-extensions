<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Handles the validation rules and authorization for the SetAsFirstRequest.
 * This class ensures that the necessary input parameters are present and valid.
 */
class StoreUpdatedMediumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'initiator_id' => ['required', 'string'],
            'media_manager_id' => ['nullable', 'string'],
            'temporary_upload' => ['required', 'string'],
            'model_type' => 'required|string',
            'model_id' => ['required_if:temporary_upload,false'],
            'medium_id' => 'required|string',
            'collection' => 'required|string',
            'file' => 'required|file',
            'image_collection' => 'required|string',
            'document_collection' => 'nullable|string',
            'youtube_collection' => 'nullable|string',
            'audio_collection' => 'nullable|string',
            'video_collection' => 'nullable|string',
        ];
    }

    protected function getRedirectUrl(): string
    {
        $url = parent::getRedirectUrl();

        if ($this->has('initiator_id')) {
            $url .= '#' . $this->input('media_manager_id');
        }

        return $url;
    }
}
