<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Rule;

class StoreSingleRequest extends StoreRequest
{
    public function rules(): array
    {
        $uploadFieldName = config('media-library-extensions.upload_field_name_single');

        $collections = $this->array('collections');

        $uploadRules = [
            'nullable',
            'file',
        ];

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

                $uploadFieldName => $uploadRules,

                'initiator_id' => ['required', 'string'],
                'media_manager_id' => ['required', 'string'],
                'instance_id' => ['nullable', 'string', 'max:64'],
            ]
        );
    }
}
