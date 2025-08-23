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
            'initiator_id' => ['required', 'string'],

            'temporary_uploads' => ['required', Rule::in(['true', 'false'])],

            'model_type' => ['required', 'string'], // model_id handled by withValidator, for conditional validation
//            modelOrClassName: $model,

            'image_collection' => ['nullable', 'string'],
            'document_collection' => ['nullable', 'string'],
            'youtube_collection' => ['nullable', 'string'],
            'video_collection' => ['nullable', 'string'],
            'audio_collection' => ['nullable', 'string'],

            'frontend_theme' => ['nullable', 'string'],

            'destroy_enabled' => ['required', Rule::in(['true', 'false'])],
            'set_as_first_enabled' => ['required', Rule::in(['true', 'false'])],
            'show_order' => ['required', Rule::in(['true', 'false'])],
            'show_menu' => ['nullable', Rule::in(['true', 'false'])],

            ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->sometimes('model_id', ['required', 'integer'], function () {
            return $this->input('temporary_uploads') === 'false';
        });
    }
}
