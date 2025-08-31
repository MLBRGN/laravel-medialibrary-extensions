<?php

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;

class GetMediaManagerTinyMceRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // TODO
//        if (! $this->expectsJson()) {
//            abort(400, 'JSON request required.');
//        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'initiator_id' => ['required', 'string'],
            'media_manager_id' => ['required', 'string'],
            'model_type' => ['required', 'string'],
            'model_id' => ['required_if:temporary_upload,false'],
            'image_collection' => 'required_without_all:video_collection,audio_collection,document_collection,youtube_collection',
            'video_collection' => 'required_without_all:image_collection,audio_collection,document_collection,youtube_collection',
            'audio_collection' => 'required_without_all:image_collection,video_collection,document_collection,youtube_collection',
            'document_collection' => 'required_without_all:image_collection,video_collection,audio_collection,youtube_collection',
            'youtube_collection' => 'required_without_all:image_collection,video_collection,audio_collection,document_collection',
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
