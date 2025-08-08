<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
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
            'temporary_upload' => ['required', 'string', Rule::in(['true', 'false'])],
            'model_type' => ['required', 'string'],
            'model_id' => ['required_if:temporary_upload,false'],
            'image_collection' => ['nullable', 'string'],
            'document_collection' => ['nullable', 'string'],
            $uploadFieldName => [
                'nullable',
                'array',
                // Apply the MaxMediaCount rule only if the model is found, else skip it gracefully
                $model ? new MaxMediaCount($model, $this->input('image_collection'), $maxItemsInCollection) : null,
            ],
            $uploadFieldName.'.media.*' => [
                'nullable',
                'mimetypes:'.implode(',', Arr::flatten(config('media-library-extensions.allowed_mimetypes'))),
                'max:'.config('media-library-extensions.max_upload_size'),
            ],
            'initiator_id' => ['required', 'string'],
        ];
    }
}
