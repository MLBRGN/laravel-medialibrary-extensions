<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Handles authorization and validation rules for media manager preview update request
 */
class GetMediaPreviewerHTMLRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'model_type' => ['required', 'string'], // model_id handled by withValidator, for conditional validation
            'model_id' => ['required_if:temporary_upload_mode,false'],
            'temporary_upload_mode' => ['required', Rule::in(['true', 'false'])],
            'initiator_id' => ['required', 'string'],
            'collections' => ['required', 'string'], // json
            'options' => ['required', 'string'], // json
        ];
    }

    //    public function withValidator(Validator $validator): void
    //    {
    //        $validator->sometimes('model_id', ['required', 'integer'], function () {
    //            return $this->input('temporary_upload_mode') === 'false';
    //        });
    //    }
}
