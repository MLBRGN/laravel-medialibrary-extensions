<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Validator;
use Mlbrgn\MediaLibraryExtensions\Rules\AllowedMediaCollections;
use Mlbrgn\MediaLibraryExtensions\Traits\ValidatesCollections;

class DestroyRequest extends MediaManagerRequest
{
    use ValidatesCollections;

    public function authorize(): bool
    {
        return $this->authorizeMediaDelete();
    }

    public function prepareForValidation(): void
    {
        if ($this->route('mediaId') && ! $this->has('mediaId')) {
            $this->merge([
                'mediaId' => $this->route('mediaId'),
            ]);
        }

        parent::prepareForValidation();
    }

    public function rules(): array
    {
        return [
            'base_id' => ['required', 'string'],
            'model_type' => ['required', 'string'],
            // When operating in temporary upload mode there is no persisted model,
            // so `model_id` must be allowed to be absent. For persisted media it is required.
            'model_id' => ['required_unless:temporary_upload_mode,true', 'string'],
            'single_media_id' => ['nullable'],
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

    public function withValidator(Validator $validator): void
    {
        $this->addCollectionsValidation($validator);
    }
}
