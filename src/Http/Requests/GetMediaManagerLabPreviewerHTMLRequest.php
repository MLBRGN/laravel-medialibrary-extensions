<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Handles authorization and validation rules for media manager preview update request
 */
class GetMediaManagerLabPreviewerHTMLRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'model_type' => ['required', 'string'],
            'model_id' => ['required_if:temporary_upload_mode,false'],
            'medium_id' => ['required', 'string'],// practically is "single medium id" (always one medium)
            'initiator_id' => ['required', 'string'],
            'part' => ['nullable', Rule::in(['original', 'base', 'all'])],
            'options' => ['required', 'string'], // json
//            'selectable' => ['required', 'string', Rule::in(['true', 'false'])],
//            'multiple' => ['required', 'string', Rule::in(['true', 'false'])],
//            'disabled' => ['required', 'string', Rule::in(['true', 'false'])],
//            'readonly' => ['required', 'string', Rule::in(['true', 'false'])],
        ];
    }
}
