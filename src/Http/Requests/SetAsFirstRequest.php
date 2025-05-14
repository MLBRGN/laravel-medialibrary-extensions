<?php

namespace Mlbrgn\SpatieMediaLibraryExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetAsFirstRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'model_type' => 'required|string',
            'model_id' => 'required|string',
            'collection_name' => ['required', 'string'],
            'medium_id' => 'required|string',
        ];
    }
}
