<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GetMediaManagerPreviewerHTMLRequest extends MediaManagerRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'model_type' => ['required', 'string'], // model_id handled by withValidator, for conditional validation
            'model_id' => ['required_if:temporary_upload_mode,false'],
            'single_media_id' => ['nullable'],
            'temporary_upload_mode' => ['required', Rule::in(['true', 'false'])],
            'base_id' => ['required', 'string'],
            'collections' => ['required', 'string'], // json
            //            'options' => ['required', 'string'], // json
            'selectable' => ['required', 'string', Rule::in(['true', 'false'])],
            'multiple' => ['required', 'string', Rule::in(['true', 'false'])],
            'disabled' => ['required', 'string', Rule::in(['true', 'false'])],
            'readonly' => ['required', 'string', Rule::in(['true', 'false'])],
            // client-provided instance IDs are not allowed; always derived from base_id
            'instance_id' => ['prohibited'],
            'data_source' => ['nullable', 'string'], // TODO if temporary required
            'theme' => ['nullable', 'string'],
            'include_debug' => ['nullable', 'string', Rule::in(['true', 'false', '1', '0'])],
            'client_token' => ['nullable', 'string'],
        ];
    }

    protected function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $id = $this->filled('single_media_id') && $this->input('single_media_id') !== 'null'
                ? (int) $this->input('single_media_id')
                : null;

            if ($id === null || $id === 'null') {
                return; // nothing to validate
            }

            $existsInMedia = Media::where('id', $id)->exists();
            $existsInTemporary = TemporaryUpload::where('id', $id)->exists();

            if (! $existsInMedia && ! $existsInTemporary) {
                $validator->errors()->add(
                    'single_media_id',
                    'The selected single_media_id does not exist in media or temporary_uploads.'
                );
            }
        });
    }
}
