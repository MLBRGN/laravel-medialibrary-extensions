<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetMediaManagerLabPreviewerHTMLRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'model_type' => ['required', 'string'],
            'model_id' => ['required_if:temporary_upload_mode,false'],
            'medium_id' => ['required', 'string'], // practically is "single medium id" (always one medium)
            'initiator_id' => ['required', 'string'],
            'part' => ['nullable', Rule::in(['original', 'base', 'all'])],
            'options' => ['required', 'string'], // json
            'data_source' => ['nullable', 'string'],
            'theme' => ['nullable', 'string'],
            'include_debug' => ['nullable', 'string', Rule::in(['true', 'false', '1', '0'])],
        ];
    }
}
