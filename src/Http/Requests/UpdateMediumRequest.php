<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Mlbrgn\MediaLibraryExtensions\Traits\ValidatesCollections;

class UpdateMediumRequest extends MediaManagerRequest
{
    use ValidatesCollections;

    public function authorize(): bool
    {
        return $this->authorizeMediaEdit();
    }

    public function rules(): array
    {
        return [
            'initiator_id' => ['required', 'string'],
            'media_manager_id' => ['required', 'string'],
            'temporary_upload_mode' => ['required', 'string', Rule::in(['true', 'false'])],
            'model_type' => 'required|string',
            'model_id' => ['required_if:temporary_upload_mode,false'],
            'medium_id' => 'required|string',
            'single_medium_id' => ['nullable'],
            'collection' => 'required|string',
            'file' => 'required|file',
            'collections' => ['required', 'array', 'min:1'],
            'collections.*' => ['nullable', 'string'],
            'instance_id' => ['nullable', 'string', 'max:64'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $this->addCollectionsValidation($validator);
    }
}
