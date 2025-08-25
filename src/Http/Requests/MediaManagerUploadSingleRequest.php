<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Rule;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxMediaCount;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxTemporaryUploadCount;

/**
 * Handles the validation rules for uploading a single media file.
 */
class MediaManagerUploadSingleRequest extends MediaManagerRequest
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
        $uploadFieldName = config('media-library-extensions.upload_field_name_single');
        $maxItemsInCollection = 1;
        $temporaryUpload = $this->input('temporary_upload', 'false');

        // Resolve model only if temporary_upload = 'false'
        $model = null;
        if ($temporaryUpload === 'false' && $this->filled('model_type') && $this->filled('model_id')) {
            $modelClass = $this->input('model_type');
            if (class_exists($modelClass)) {
                $model = $modelClass::find($this->input('model_id'));
            }
        }

        $collectionFields = array_filter([
            $this->input('image_collection'),
            $this->input('document_collection'),
            $this->input('video_collection'),
            $this->input('audio_collection'),
            $this->input('youtube_collection'),
        ]);

        // NOTE: mimetypes checks for mimetype in file, mimes only checks extension
        return [
            'temporary_upload' => ['required', 'string', Rule::in(['true', 'false'])],
            'model_type' => ['required', 'string'],
            'model_id' => ['required_if:temporary_upload,false'],
            'image_collection' => 'required_without_all:video_collection,audio_collection,document_collection,youtube_collection',
            'video_collection' => 'required_without_all:image_collection,audio_collection,document_collection,youtube_collection',
            'audio_collection' => 'required_without_all:image_collection,video_collection,document_collection,youtube_collection',
            'document_collection' => 'required_without_all:image_collection,video_collection,audio_collection,youtube_collection',
            'youtube_collection' => 'required_without_all:image_collection,video_collection,audio_collection,document_collection',
            $uploadFieldName => [
                'nullable',
                'file',
                $temporaryUpload === 'false'
                    ? new MaxMediaCount($model, $collectionFields, $maxItemsInCollection)
                    : new MaxTemporaryUploadCount($collectionFields, $maxItemsInCollection),
            ],
            'initiator_id' => ['required', 'string'],
            'media_manager_id' => ['required', 'string'],
        ];
    }
}
