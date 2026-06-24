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
                    'max:'.(config('medialibrary-extensions.max_upload_size') / 1024),
                ],

                'initiator_id' => ['required', 'string'],
                'media_manager_id' => ['required', 'string'],
                'instance_id' => ['nullable', 'string', 'max:64'],
                'data_source' => [
                    Rule::requiredIf(fn () => $this->input('temporary_upload_mode') === 'true'),
                    'string',
                ],
            ]
        );
    }
}
