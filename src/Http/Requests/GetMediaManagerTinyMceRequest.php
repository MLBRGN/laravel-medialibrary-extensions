<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class GetMediaManagerTinyMceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'initiator_id' => ['required', 'string'],
            'media_manager_id' => ['required', 'string'],
            'model_type' => ['required', 'string'],
            'model_id' => ['required_if:temporary_upload_mode,false'],
            'collections' => ['required', 'array'],
            'collections.*' => ['nullable', 'string'],
//            'image_collection' => 'required_without_all:video_collection,audio_collection,document_collection,youtube_collection',
//            'video_collection' => 'required_without_all:image_collection,audio_collection,document_collection,youtube_collection',
//            'audio_collection' => 'required_without_all:image_collection,video_collection,document_collection,youtube_collection',
//            'document_collection' => 'required_without_all:image_collection,video_collection,audio_collection,youtube_collection',
//            'youtube_collection' => 'required_without_all:image_collection,video_collection,audio_collection,document_collection',
            'temporary_upload_mode' => ['required', Rule::in(['true', 'false'])],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
