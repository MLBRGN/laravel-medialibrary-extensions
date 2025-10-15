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
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'model_type' => ['required', 'string'], // model_id handled by withValidator, for conditional validation
            'model_id' => ['required_if:temporary_upload_mode,false'],
            'temporary_upload_mode' => ['required', Rule::in(['true', 'false'])],
            'initiator_id' => ['required', 'string'],
            'collections' => ['required', 'string'], // json
            'options' => ['required', 'string'], // json

            //            'frontend_theme' => ['nullable', 'string'],
            //            'show_destroy_button' => ['required', Rule::in(['true', 'false'])],
            //            'show_set_as_first_button' => ['required', Rule::in(['true', 'false'])],
            //            'show_media_edit_button' => ['required', Rule::in(['true', 'false'])],
            //            'show_order' => ['required', Rule::in(['true', 'false'])],
            //            'show_menu' => ['nullable', Rule::in(['true', 'false'])],
            //            'selectable' => ['nullable', Rule::in(['true', 'false'])],
            //            'image_collection' => 'required_without_all:video_collection,audio_collection,document_collection,youtube_collection',
            //            'video_collection' => 'required_without_all:image_collection,audio_collection,document_collection,youtube_collection',
            //            'audio_collection' => 'required_without_all:image_collection,video_collection,document_collection,youtube_collection',
            //            'document_collection' => 'required_without_all:image_collection,video_collection,audio_collection,youtube_collection',
            //            'youtube_collection' => 'required_without_all:image_collection,video_collection,audio_collection,document_collection',
        ];
    }

    //    public function withValidator(Validator $validator): void
    //    {
    //        $validator->sometimes('model_id', ['required', 'integer'], function () {
    //            return $this->input('temporary_upload_mode') === 'false';
    //        });
    //    }
}
