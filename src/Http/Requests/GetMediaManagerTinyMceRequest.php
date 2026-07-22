<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Mlbrgn\MediaLibraryExtensions\Rules\AllowedMediaCollections;

class GetMediaManagerTinyMceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'base_id' => ['required', 'string'],
            'model_type' => ['required', 'string'],
            'model_id' => ['required_if:temporary_upload_mode,false'],
            'collections' => [
                'required',
                'string',
                // TODO look at this
//                new AllowedMediaCollections(
//                    $this->mediaModel(),
//                )
            ], // json
            'options' => ['required', 'string'], // json
            'temporary_upload_mode' => ['required', Rule::in(['true', 'false'])],
            'multiple' => ['required', Rule::in(['true', 'false'])],
            'data_source' => ['nullable', 'string'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $acceptHeader = $this->header('Accept', '');
        $wantsHtml = str_contains($acceptHeader, 'text/html');

        if ($wantsHtml) {
            throw new HttpResponseException(
                response()->view(
                    'medialibrary-extensions::errors.error',
                    [
                        'title' => __('medialibrary-extensions::messages.validation_error'),
                        'message' => __('medialibrary-extensions::messages.invalid_configuration'),
                        'errors' => $validator->errors()->all(),
                    ],
                    422
                )
            );
        }

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
