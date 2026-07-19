<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Rule;

class StoreMultipleRequest extends StoreRequest
{
    public function rules(): array
    {
        $collections = $this->array('collections');

        $uploadRules = [
            'nullable',
            'array',
        ];

        if ($rule = $this->uploadLimitRule(
            $collections,
            config('medialibrary-extensions.max_items_in_shared_media_collections')
        )) {
            $uploadRules[] = $rule;
        }

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

                'media.*' => [
                    'nullable',
                    'file',
                    'max:'.(config('medialibrary-extensions.max_upload_size') / 1024), // max upload size in kilobytes
                    new \Mlbrgn\MediaLibraryExtensions\Rules\ImageDimensionsWithinConfig(),
                ],

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
