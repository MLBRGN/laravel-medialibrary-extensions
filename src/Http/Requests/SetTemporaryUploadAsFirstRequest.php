<?php

/** @noinspection PhpMissingParentCallCommonInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

class SetTemporaryUploadAsFirstRequest extends MediaManagerRequest
{
    public function authorize(): bool
    {
        return $this->authorizeMediaEdit();
    }

    public function rules(): array
    {
        return [
            'model_type' => 'required|string',
            'model_id' => 'nullable',
            'target_media_collection' => ['required', 'string'],
            'medium_id' => 'required|string',
            'single_media_id' => ['nullable'],
            'initiator_id' => ['required', 'string'],
            'media_manager_id' => ['required', 'string'],
            'collections' => ['required', 'array', 'min:1'],
            'collections.*' => ['nullable', 'string'],
            'data_source' => ['nullable', 'string'],
        ];
    }
}
