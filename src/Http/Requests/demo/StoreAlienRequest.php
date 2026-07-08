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
//            'name' => ['required', 'string'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $this->addCollectionsValidation($validator);
    }
}
