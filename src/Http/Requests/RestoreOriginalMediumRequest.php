<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Mlbrgn\MediaLibraryExtensions\Traits\ValidatesCollections;

/**
 * Handles authorization and validation rules for media manager medium destroy requests.
 */
class RestoreOriginalMediumRequest extends MediaManagerRequest
{
    use ValidatesCollections;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
//            'initiator_id' => ['required', 'string'],
//            'temporary_upload_mode' => ['required', 'string', Rule::in(['true', 'false'])],
//            'model_type' => 'required|string',
//            'model_id' => ['required_if:temporary_upload_mode,false'],
//            'single_medium_id' => ['nullable'],

//            'media_manager_id' => ['required', 'string'],
//            'medium_id' => 'required|string',
//            'collection' => 'required|string',
//            'file' => 'required|file',
//            'collections' => ['required', 'array', 'min:1'],
//            'collections.*' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $this->addCollectionsValidation($validator);
    }
}
