<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Validation\Rule;
use Mlbrgn\MediaLibraryExtensions\Rules\AllowedMediaCollections;
use Mlbrgn\MediaLibraryExtensions\Rules\YouTubeUrl;

class StoreYouTubeVideoRequest extends StoreRequest
{
    public function rules(): array
    {

        // NOTE: mimetypes checks for mimetype in file, mimes only checks extension
        return [
            'temporary_upload_mode' => ['required', 'string', Rule::in(['true', 'false'])],
            'model_type' => ['required', 'string'],
            'model_id' => ['required_if:temporary_upload_mode,false'],
            'collections' => [
                'required',
                'array',
                'min:1',
                // TODO look at this
//                new AllowedMediaCollections(
//                    $this->mediaModel(),
//                )
            ],
            'collections.*' => ['nullable', 'string'],
            'youtube_url' => ['nullable', 'url', new YouTubeUrl],
            'base_id' => ['required', 'string'],
            'multiple' => ['required', Rule::in(['true', 'false'])],
            'data_source' => [
                Rule::requiredIf(fn () => $this->input('temporary_upload_mode') === 'true'),
                'string',
            ],
        ];
    }

    protected function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Reject legacy identifier keys if present (keep check for instance_id only)
            foreach (['instance_id'] as $legacyKey) {
                if ($this->has($legacyKey)) {
                    $validator->errors()->add($legacyKey, 'Legacy identifier "'.$legacyKey.'" is not allowed. Use base_id.');
                }
            }
        });
    }
}
