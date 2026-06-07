<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

abstract class MediaManagerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    protected function resolveModelClass(): ?string
    {
        // TODO look at this, does it need to be a string?
        $modelClass = (string) $this->string('model_type')->trim();

        if (class_exists(Relation::class)) {
            $modelClass = Relation::getMorphedModel($modelClass) ?? $modelClass;
        }

        if (! class_exists($modelClass)) {
            return null;
        }

        if (! is_subclass_of($modelClass, HasMediaExtended::class)) {
            return null;
        }

        return $modelClass;
    }

    protected function resolveModel(): ?HasMediaExtended
    {
        if ($this->isTemporaryUpload()) {
            return null;
        }

        $mediaService = app(MediaService::class);
        $modelClass = $this->resolveModelClass();
        $modelId = $this->input('model_id');
        $dataSource = $this->input('data_source');
        $model = $mediaService->findMediaModel($modelClass, $modelId, $dataSource);

        return $model;
    }

    protected function isTemporaryUpload(): bool
    {
        return $this->boolean('temporary_upload_mode');
    }

    protected function authorizeMediaUpload(): bool
    {
        $modelClass = $this->resolveModelClass();

        if (! $modelClass || ! $modelClass::allowsMediaUploads()) {
            return false;
        }

        if (! $this->requestedCollectionsAreAllowed()) {
            return false;
        }

        if ($this->isTemporaryUpload()) {
            return true;
        }

        $model = $this->resolveModel();

        return $model !== null
            && $model->allowsMediaUploadFrom($this->user());
    }

    protected function authorizeMediaDelete(): bool
    {
        return true;

        $modelClass = $this->resolveModelClass();

        if (! $modelClass || ! $modelClass::allowsMediaDeletes()) {
            return false;
        }

        if (! $this->requestedCollectionsAreAllowed()) {
            return false;
        }

        $model = $this->resolveModel();

        return $model !== null
            && $model->allowsMediaDeletesFrom($this->user());
    }

    protected function authorizeMediaEdit(): bool
    {
        $modelClass = $this->resolveModelClass();

        if (! $modelClass || ! $modelClass::allowsMediaEdits()) {
            return false;
        }

        if (! $this->requestedCollectionsAreAllowed()) {
            return false;
        }

        if ($this->isTemporaryUpload()) {
            return true;
        }

        $model = $this->resolveModel();

        return $model !== null
            && $model->allowsMediaEditsFrom($this->user());
    }

    protected function requestedCollectionsAreAllowed(): bool
    {
        $model = $this->resolveModel();

        if (! $model) {
            return $this->isTemporaryUpload()
                ? $this->temporaryRequestedCollectionsAreAllowed()
                : false;
        }

        return $this->collectionsAreAllowed(
            $this->requestedCollectionNames(),
            $model->allowedMediaCollections()
        );
    }

    protected function temporaryRequestedCollectionsAreAllowed(): bool
    {
        $modelClass = $this->resolveModelClass();

        if (! $modelClass) {
            return false;
        }

        $model = new $modelClass;

        if (! $model instanceof HasMediaExtended) {
            return false;
        }

        return $this->collectionsAreAllowed(
            $this->requestedCollectionNames(),
            $model->allowedMediaCollections()
        );
    }

    protected function requestedCollectionNames(): array
    {
        $collections = [];

        if ($this->has('collections')) {
            $inputCollections = $this->input('collections');

            if (is_string($inputCollections)) {
                $decodedCollections = json_decode($inputCollections, true);
                $inputCollections = is_array($decodedCollections) ? $decodedCollections : [];
            }

            if (is_array($inputCollections)) {
                $collections = array_merge($collections, $this->flattenCollectionNames($inputCollections));
            }
        }

        if ($this->filled('collection')) {
            $collections[] = (string) $this->input('collection');
        }

        if ($this->filled('target_media_collection')) {
            $collections[] = (string) $this->input('target_media_collection');
        }

        return array_values(array_unique(array_filter($collections)));
    }

    protected function flattenCollectionNames(array $collections): array
    {
        $names = [];

        foreach ($collections as $key => $value) {
            if (is_string($value)) {
                $names[] = $value;

                continue;
            }

            if (is_array($value)) {
                $names = array_merge($names, $this->flattenCollectionNames($value));
            }
        }

        return $names;
    }

    protected function collectionsAreAllowed(array $requestedCollections, array $allowedCollections): bool
    {
        $requestedCollections = array_values(array_unique(array_filter($requestedCollections)));
        $allowedCollections = array_values(array_unique(array_filter($allowedCollections)));

        if ($requestedCollections === []) {
            return true;
        }

        if ($allowedCollections === []) {
            return true;
        }

        return empty(array_diff($requestedCollections, $allowedCollections));
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

    public function prepareForValidation(): void
    {
        if (
            $this->input('data_source') === 'null'
            || $this->input('data_source') === 'undefined'
        ) {
            $this->merge(['data_source' => null]);
        }
    }

    protected function passedValidation(): void
    {
        //
    }
}
