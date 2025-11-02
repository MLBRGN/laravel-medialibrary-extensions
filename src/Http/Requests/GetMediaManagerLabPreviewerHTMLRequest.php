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
            'medium_id' => ['required', 'string'],
            'initiator_id' => ['required', 'string'],
            'part' => ['nullable', Rule::in(['original', 'base', 'all'])],
        ];
    }
}
