<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests\demo;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
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
