<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

/**
 * Handles authorization and validation rules for media manager temporary upload destroy requests.
 */
class MediaManagerTemporaryUploadDestroyRequest extends MediaManagerRequest
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
        return [
            'initiator_id' => ['required', 'string'],
        ];
    }
}
