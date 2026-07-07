<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Rule;
use Mlbrgn\MediaLibraryExtensions\Rules\ImageDimensionsWithinConfig;

class StoreSingleRequest extends StoreRequest
{
    public function rules(): array
    {
        $collections = $this->array('collections');

        $uploadRules = [
            'nullable',
            'file',
        ];

        if ($maxSize = config('medialibrary-extensions.max_upload_size')) {
            $uploadRules[] = 'max:'.$maxSize / 1024;
        }

        if ($rule = $this->uploadLimitRule($collections, 1)) {
            $uploadRules[] = $rule;
        }

        // Enforce image dimension limits from config when the uploaded file is an image.
        $uploadRules[] = new ImageDimensionsWithinConfig();

        return array_merge(
            $this->modelRules(),
            [
                'temporary_upload_mode' => [
                    'required',
                    'string',
                    Rule::in(['true', 'false']),
                ],

                'collections' => ['required', 'array', 'min:1'],
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
    }

    protected function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        // No legacy identifier checks remain; clients must send only base_id. Instance IDs are prohibited via rules().
    }
}
