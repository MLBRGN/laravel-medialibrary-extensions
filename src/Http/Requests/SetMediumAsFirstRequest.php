<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

class SetMediumAsFirstRequest extends MediaManagerRequest
{

    public function authorize(): bool
    {
        return $this->authorizeMediaEdit();
    }

    public function rules(): array
    {
        return [
            'model_type' => 'required|string',
            'model_id' => 'required|string',
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
