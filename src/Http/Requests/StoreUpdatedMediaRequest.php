<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Mlbrgn\MediaLibraryExtensions\Traits\ValidatesCollections;

class StoreUpdatedMediaRequest extends MediaManagerRequest
{
    use ValidatesCollections;

    public function authorize(): bool
    {
        return $this->authorizeMediaEdit();
    }

    public function rules(): array
    {
        return [
            'base_id' => ['required', 'string'],
            'base_id' => ['required', 'string'],
            'temporary_upload_mode' => ['required', 'string', Rule::in(['true', 'false'])],
            'model_type' => 'required|string',
            'model_id' => ['required_if:temporary_upload_mode,false'],
            'medium_id' => 'required|string',
            'single_media_id' => ['nullable'],
            'collection' => 'required|string',
            'file' => 'required|file',
            'collections' => ['nullable', 'array'],
            'collections.*' => ['nullable', 'string'],
            'instance_id' => ['nullable', 'string', 'max:64'],
            'data_source' => ['nullable', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $collections = $this->input('collections');
        if (is_array($collections) && ! empty($collections)) {
            $this->addCollectionsValidation($validator);
        }
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        Log::error('mle.imageEditor.save.validation_failed', [
            'errors' => $validator->errors()->toArray(),
            'input_keys' => array_keys($this->all()),
            'route' => $this->path(),
        ]);

        parent::failedValidation($validator);
    }
}
