<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Validator;
use Mlbrgn\MediaLibraryExtensions\Traits\ValidatesCollections;

class DestroyRequest extends MediaManagerRequest
{
    use ValidatesCollections;

    public function authorize(): bool
    {
        return $this->authorizeMediaDelete();
    }

    public function rules(): array
    {
        return [
            'initiator_id' => ['required', 'string'],
            'media_manager_id' => ['required', 'string'],
            'model_type' => ['required', 'string'],
            'model_id' => ['required', 'string'],
            'single_medium_id' => ['nullable'],
            'collections' => ['required', 'array', 'min:1'],
            'collections.*' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $this->addCollectionsValidation($validator);
    }
}
