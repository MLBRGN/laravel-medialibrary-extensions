<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Mlbrgn\MediaLibraryExtensions\Rules\AllowedMediaCollections;
use Mlbrgn\MediaLibraryExtensions\Rules\ImageDimensionsWithinConfig;

class StoreSingleRequest extends StoreRequest
{
    public function rules(): array
    {
        $collections = $this->array('collections');
        $validationModel = $this->resolveValidationModel();

        $uploadRules = [
            'nullable',
            'file',
        ];

        if ($maxSize = config('medialibrary-extensions.max_upload_size')) {
            $uploadRules[] = 'max:'.$maxSize / 1024; // max upload size in kilobytes
        }

        if ($rule = $this->uploadLimitRule($collections, 1)) {
            $uploadRules[] = $rule;
        }

        // Enforce image dimension limits from config when the uploaded file is an image.
        $uploadRules[] = new ImageDimensionsWithinConfig;

        $rules = array_merge(
            $this->modelRules(),
            [
                'temporary_upload_mode' => [
                    'required',
                    'string',
                    Rule::in(['true', 'false']),
                ],

                'collections' => [
                    'required',
                    'array',
                    'min:1',
                ],
                'collections.*' => ['nullable', 'string'],

                'media' => $uploadRules,

                'base_id' => ['required', 'string'],
                // client-provided instance IDs are not allowed; always derived from base_id
                'instance_id' => ['prohibited'],
                'data_source' => [
                    Rule::requiredIf(fn () => $this->input('temporary_upload_mode') === 'true'),
                    'string',
                ],
            ]
        );

        if ($validationModel) {
            $rules['collections'][] = new AllowedMediaCollections($validationModel);
        }

        return $rules;
    }

    public function messages(): array
    {
        $maxBytes = config('medialibrary-extensions.max_upload_size');

        return [
            'media.max' => __('medialibrary-extensions::validation.media_max', [
                'max' => mle_human_filesize($maxBytes),
            ]),
        ];
    }

    protected function withValidator(Validator $validator): void
    {
        // No legacy identifier checks remain; clients must send only base_id. Instance IDs are prohibited via rules().
    }
}
