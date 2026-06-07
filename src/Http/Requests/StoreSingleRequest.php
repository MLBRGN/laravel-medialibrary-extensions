<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Rule;

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

                'initiator_id' => ['required', 'string'],
                'media_manager_id' => ['required', 'string'],
                'instance_id' => ['nullable', 'string', 'max:64'],
                'data_source' => ['nullable', 'string'],
            ]
        );
    }
}
