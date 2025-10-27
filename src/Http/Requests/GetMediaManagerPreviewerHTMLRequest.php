<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Handles authorization and validation rules for media manager preview update request
 */
class GetMediaManagerPreviewerHTMLRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'model_type' => ['required', 'string'], // model_id handled by withValidator, for conditional validation
            'model_id' => ['required_if:temporary_upload_mode,false'],
            'single_medium_id' => ['nullable'],
            'temporary_upload_mode' => ['required', Rule::in(['true', 'false'])],
            'initiator_id' => ['required', 'string'],
            'collections' => ['required', 'string'], // json
            'options' => ['required', 'string'], // json
            'selectable' => ['required', 'string', Rule::in(['true', 'false'])],
            'multiple' => ['required', 'string', Rule::in(['true', 'false'])],
            'disabled' => ['required', 'string', Rule::in(['true', 'false'])],
            'readonly' => ['required', 'string', Rule::in(['true', 'false'])],
        ];
    }

//    protected function prepareForValidation(): void
//    {
//        $this->merge([
//            'single_medium_id' => $this->filled('single_medium_id') ? $this->input('single_medium_id') : null,
//        ]);
//    }

    protected function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $id = $this->filled('single_medium_id') && $this->input('single_medium_id') !== 'null'
                ? (int) $this->input('single_medium_id')
                : null;

            if ($id === null || $id === 'null') {
                return; // nothing to validate
            }

            $existsInMedia = Media::where('id', $id)->exists();
            $existsInTemporary = TemporaryUpload::where('id', $id)->exists();

            if (! $existsInMedia && ! $existsInTemporary) {
                $validator->errors()->add(
                    'single_medium_id',
                    'The selected single_medium_id does not exist in media or temporary_uploads.'
                );
            }
        });
    }
}
