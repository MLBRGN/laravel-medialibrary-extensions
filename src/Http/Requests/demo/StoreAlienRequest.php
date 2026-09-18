<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests\demo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Mlbrgn\MediaLibraryExtensions\Rules\MinMediaCount;
use Mlbrgn\MediaLibraryExtensions\Traits\ValidatesCollections;

class StoreAlienRequest extends FormRequest
{
    use ValidatesCollections;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alien_multiple_min_media_count' => [
                'sometimes',
                new MinMediaCount(
                    $this->input('id') ? \Mlbrgn\MediaLibraryExtensions\Models\demo\Alien::find($this->input('id')) : null,
                    ['alien-multiple-images'],
                    2,
                    $this->input('instance_id'),
                    $this->input('data_source', 'default')
                ),
            ],
            // Demo UI props and context
            //            'data_source' => ['sometimes', 'string', 'in:default,demo'],
            //            'client_token' => ['sometimes', 'string'],
            //            'instance_id' => ['sometimes', 'string'],
            //            'theme' => ['sometimes', 'string'],
            //            'use_xhr' => ['sometimes', 'string', 'in:0,1,true,false'],
            //            // Demo model field (currently a hidden dummy value)
            //            'name' => ['sometimes', 'string', 'max:255'],

        ];
    }

    public function withValidator(Validator $validator): void
    {
        $this->addCollectionsValidation($validator);
    }
}
