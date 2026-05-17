<?php

/** @noinspection PhpMissingParentCallCommonInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

class SetTemporaryUploadAsFirstRequest extends MediaManagerRequest
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
            'single_medium_id' => ['nullable'],
            'initiator_id' => ['required', 'string'],
            'media_manager_id' => ['required', 'string'],
            'collections' => ['required', 'array', 'min:1'],
            'collections.*' => ['nullable', 'string'],
        ];
    }
}
