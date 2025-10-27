<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;

abstract class MediaManagerRequest extends FormRequest
{
    public function rules(): array
    {
        return [];
    }

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
     * Override the redirect URL to include the media manager ID.
     */
    protected function getRedirectUrl(): string
    {
        $url = parent::getRedirectUrl();

        if ($this->has('media_manager_id')) {
            $url .= '#'.$this->input('media_manager_id');
        }

        return $url;
    }

    protected function failedValidation(Validator $validator)
    {
        $request = $this; // the FormRequest itself
        $initiatorId = $request->input('initiator_id') ?? 'unknown';
        $mediaManagerId = $request->input('media_manager_id') ?? 'unknown';
        $errors = $validator->errors();

        $response = MediaResponse::error(
            $request,
            $initiatorId,
            $mediaManagerId,
            $errors->first(),
            ['errors' => $errors->messages()]
        );

        // Force 422 for JSON responses
        if ($request->expectsJson()) {
            $response->setStatusCode(422);
        }

        throw new ValidationException($validator, $response);
    }
}
