<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

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
            'collections' => ['required', 'string'], // json
            'options' => ['required', 'string'], // json
            'temporary_upload_mode' => ['required', Rule::in(['true', 'false'])],
            'multiple' => ['required', Rule::in(['true', 'false'])],
            'data_source' => ['nullable', 'string'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();

        // Determine if this request expects HTML (TinyMCE iframe)
        $acceptHeader = $this->header('Accept', '');
        $wantsHtml = str_contains($acceptHeader, 'text/html');

        if ($wantsHtml) {
            // Return an HTML response so TinyMCE doesn't render raw JSON
            $html = view('medialibrary-extensions::components.shared.tinymce-error', [
                'message' => __('medialibrary-extensions::messages.invalid_configuration'),
                'errors' => $errors,
            ])->render();

            throw new HttpResponseException(
                response($html, 422)
            );
        }

        // Fallback: return JSON for API or AJAX calls
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
