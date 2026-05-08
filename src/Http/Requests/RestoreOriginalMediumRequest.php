<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Mlbrgn\MediaLibraryExtensions\Traits\ValidatesCollections;

class RestoreOriginalMediumRequest extends MediaManagerRequest
{
    use ValidatesCollections;

    public function authorize(): bool
    {
        return $this->authorizeMediaEdit();
    }

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
