<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Mlbrgn\MediaLibraryExtensions\Rules\MaxMediaCount;

/**
 * Handle the validation and authorization for uploading multiple media files.
 */
class MediaManagerUploadMultipleRequest extends FormRequest
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
        $uploadFieldName = config('media-library-extensions.upload_field_name_multiple');
        $maxItemsInCollection = config('media-library-extensions.max_items_in_collection');

        // Try to resolve the model instance to pass to the rule
        $model = null;
        if ($this->filled('model_type') && $this->filled('model_id')) {
            $modelClass = $this->input('model_type');
            if (class_exists($modelClass)) {
                $model = $modelClass::find($this->input('model_id'));
            }
        }

        // NOTE: mimetypes checks for mimetype in file, mimes only checks extension
        return [
            'model_type' => ['required', 'string'],
            'model_id' => ['required', 'string'],
            'upload_to_collection' => ['required', 'string'],
            'image_collection' => ['nullable', 'string'],
            'document_collection' => ['nullable', 'string'],
            $uploadFieldName => [
                'nullable',
                'array',
                // Apply the MaxMediaCount rule only if model is found, else skip it gracefully
                $model ? new MaxMediaCount($model, $this->input('upload_to_collection'), $maxItemsInCollection) : null,
            ],
            $uploadFieldName.'.media.*' => [
                'nullable',
                'mimetypes:'.implode(',', Arr::flatten(config('media-library-extensions.allowed_mimetypes'))),
                'max:'.config('media-library-extensions.max_upload_size'),
            ],
            'youtube_url' => ['nullable', 'url', 'regex:/^https?:\/\/(www\.)?(youtube\.com|youtu\.be)\//'],
            'initiator_id' => ['required', 'string'],
        ];
    }
}
