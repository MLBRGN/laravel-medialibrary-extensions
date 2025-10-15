<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Mlbrgn\MediaLibraryExtensions\Http\Requests\Traits\ValidatesCollections;
use Illuminate\Validation\Validator;

/**
 * Handles authorization and validation rules for media manager medium destroy requests.
 */
class DestroyRequest extends MediaManagerRequest
{

    use ValidatesCollections;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'initiator_id' => ['required', 'string'],
            'media_manager_id' => ['required', 'string'],
            'collections' => ['required', 'array'],
            'collections.*' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $this->addCollectionsValidation($validator);
    }
}
