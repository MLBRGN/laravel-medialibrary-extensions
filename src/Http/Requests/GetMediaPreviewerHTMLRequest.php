<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Handles authorization and validation rules for media manager destruction requests.
 */
class GetMediaPreviewerHTMLRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'model_type' => ['required', 'string'],
            'model_id' => ['required', 'integer'],
            'collection' => ['required', 'string'],
            'youtube_collection' => ['nullable', 'string'],
            'document_collection' => ['nullable', 'string'],
            'initiator_id' => ['required', 'string'],
        ];
    }
}
