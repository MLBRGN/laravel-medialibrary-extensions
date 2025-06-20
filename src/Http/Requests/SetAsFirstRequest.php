<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Handles the validation rules and authorization for the SetAsFirstRequest.
 * This class ensures that the necessary input parameters are present and valid.
 */
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
            'initiator_id' => ['required', 'string'],
        ];
    }
}
