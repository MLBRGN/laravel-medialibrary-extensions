<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Rule;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxMediaCount;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxTemporaryUploadCount;

/**
 * Handles the validation rules for uploading a single media file.
 */
class StoreSingleRequest extends MediaManagerRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $uploadFieldName = config('media-library-extensions.upload_field_name_single');
        $maxItemsInCollection = 1;
        $temporaryUploadMode = $this->input('temporary_upload_mode', 'false');

        // Resolve model only if temporary_upload_mode = 'false'
        $model = null;
        if ($temporaryUploadMode === 'false' && $this->filled('model_type') && $this->filled('model_id')) {
            $modelClass = $this->input('model_type');
            if (class_exists($modelClass)) {
                $model = $modelClass::find($this->input('model_id'));
            }
        }

        $collections = $this->array('collections');

        // NOTE: mimetypes checks for mimetype in file, mimes only checks extension
        return [
            'temporary_upload_mode' => ['required', 'string', Rule::in(['true', 'false'])],
            'model_type' => ['required', 'string'],
            'model_id' => ['required_if:temporary_upload_mode,false'],
            'collections' => ['required', 'array', 'min:1'],
            'collections.*' => ['nullable', 'string'],
            $uploadFieldName => [
                'nullable',
                'file',
                $temporaryUploadMode === 'false'
                    ? new MaxMediaCount($model, $collections, $maxItemsInCollection)
                    : new MaxTemporaryUploadCount($collections, $maxItemsInCollection),
            ],
            'initiator_id' => ['required', 'string'],
            'media_manager_id' => ['required', 'string'],
        ];
    }
}
