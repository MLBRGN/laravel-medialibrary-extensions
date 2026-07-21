<?php

/** @noinspection PhpMissingParentCallCommonInspection */
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Mlbrgn\MediaLibraryExtensions\Rules\AllowedMediaCollections;

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
            'base_id' => ['required', 'string'],
            'collections' => [
                'required',
                'array',
                'min:1',
                // TODO look at this
//                new AllowedMediaCollections(
//                    $this->mediaModel(),
//                )
            ],
            'collections.*' => ['nullable', 'string'],
            'data_source' => ['nullable', 'string'],
        ];
    }
}
