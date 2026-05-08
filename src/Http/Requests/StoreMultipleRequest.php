<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class StoreMultipleRequest extends StoreRequest
{

    public function rules(): array
    {
        $uploadFieldName = config('media-library-extensions.upload_field_name_multiple');

        $collections = $this->array('collections');

        $uploadRules = [
            'nullable',
            'array',
        ];

        if ($rule = $this->uploadLimitRule(
            $collections,
            config('media-library-extensions.max_items_in_shared_media_collections')
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

                $uploadFieldName => $uploadRules,

                $uploadFieldName.'.media.*' => [
                    'nullable',
                    'mimetypes:' . implode(',', Arr::flatten(
                        config('media-library-extensions.allowed_mimetypes')
                    )),
                    'max:' . config('media-library-extensions.max_upload_size'),
                ],

                'initiator_id' => ['required', 'string'],
                'media_manager_id' => ['required', 'string'],
                'instance_id' => ['nullable', 'string', 'max:64'],
            ]
        );
    }
}
