<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
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

    // TODO Refactor model resolution responsibilities.
    //
    // Goal:
    // - The Request should only know about request input and authorization.
    // - MediaService should own all model resolution logic.
    //
    // Steps:
    // 1. Move model_type -> model class resolution (including morph map support)
    //    into MediaService.
    // 2. Move model instantiation into MediaService.
    // 3. Move loading an existing model by ID into MediaService.
    // 4. Centralize exception handling (ModelNotFoundException,
    //    QueryException, etc.) inside MediaService.
    // 5. Let this Request simply ask MediaService for either:
    //      - an existing model, or
    //      - a new model instance (for temporary uploads).
    //
    // Desired end result:
    // $model = $mediaService->resolveRequestModel(...);
    // or
    // $model = $mediaService->makeRequestModel(...);
    //
    // The Request should no longer need to know about:
    // - Relation::getMorphedModel()
    // - class_exists()
    // - HasMediaExtended validation
    // - database connections
    // - model lookup exception handling


    // TODO Remove.
    // This should eventually delegate entirely to MediaService.
    protected function mediaModel(): ?HasMediaExtended
    {
        if ($model = $this->resolveModel()) {
            return $model;
        }

        $modelClass = $this->resolveModelClass();

        if (! $modelClass) {
            return null;
        }

        // Delegate instantiation and connection assignment to MediaService
        $mediaService = app(MediaService::class);
        $dataSource = $this->input('data_source') ?? 'default';

        /** @var HasMediaExtended $instance */
        $instance = $mediaService->makeModel($modelClass, $dataSource);

        return $instance;
    }

    // Delegates model class resolution to MediaService to keep a single source
    // of truth for morph map expansion and interface validation.
    protected function resolveModelClass(): ?string
    {
        $mediaService = app(MediaService::class);
        $dataSource = $this->input('data_source') ?? 'default';

        $inputType = (string) $this->string('model_type')->trim();

        try {
            $resolved = $mediaService->resolveModelReference($inputType, $dataSource);

            return $resolved->modelType;
        } catch (\Throwable $e) {
            // Swallow and return null so callers can decide on 403/422 behavior
            // while we keep logs for debugging.
            Log::warning('Failed to resolve model class from request.', [
                'exception' => $e->getMessage(),
                'model_type' => $inputType,
                'data_source' => $dataSource,
            ]);

            return null;
        }
    }

    // TODO Move to MediaService.
    // Loading a model, selecting the correct connection and handling lookup
    // exceptions are service responsibilities, not request responsibilities.
    protected function resolveModel(): ?HasMediaExtended
    {
        if ($this->isTemporaryUpload()) {
            return null;
        }

        $mediaService = app(MediaService::class);
        $modelClass = $this->resolveModelClass();
        $modelId = $this->input('model_id');
        $dataSource = $this->input('data_source') ?? 'default';

        try {
            // Use the new API name while preserving behavior
            $model = $mediaService->findModel($modelClass, $modelId, $dataSource);
        } catch (ModelNotFoundException $e) {
            // During authorization checks we want to gracefully return `null`
            // so that `authorize*` methods can respond with `false` instead of
            // throwing a 404. We still log the failure for observability.
            Log::warning('Failed to resolve media model during request processing.', [
                'exception' => $e,
                'model_type' => $this->input('model_type'),
                'model_id' => $this->input('model_id'),
                'data_source' => $this->input('data_source'),
            ]);

            return null;
        }
        catch (QueryException $e) {
            // Same as above: return `null` to let callers decide how to react
            // (e.g., authorization should return false). We log for debugging.
            Log::error('Database query error while resolving media model: '.$e->getMessage(), [
                'model_type' => $this->input('model_type'),
                'model_id' => $this->input('model_id'),
                'data_source' => $this->input('data_source'),
            ]);

            return null;
        }

        return $model;
    }

    protected function isTemporaryUpload(): bool
    {
        return $this->boolean('temporary_upload_mode');
    }

    protected function authorizeMediaUpload(): bool
    {
        return $this->authorizeMediaAction('upload');
    }

    protected function authorizeMediaDelete(): bool
    {
        return $this->authorizeMediaAction('delete');
    }

    protected function authorizeMediaEdit(): bool
    {
        return $this->authorizeMediaAction('edit');
    }

    // TODO After model resolution has moved to MediaService, this method should
    // only contain authorization logic:
    //
    // 1. Allow temporary upload abilities where appropriate.
    // 2. Ask MediaService for the model.
    // 3. Call canPerformMediaAction().
    //
    // It should not perform any model resolution itself.
    protected function authorizeMediaAction(string $ability): bool
    {
        $modelClass = $this->resolveModelClass();

        if (! $modelClass) {
            return false;
        }

        if ($this->isTemporaryUpload() && in_array($ability, ['upload', 'edit'])) {
            return true;
        }

        $model = $this->resolveModel();

        return $model?->canPerformMediaAction($ability, $this->user()) ?? false;
    }

//    protected function requestedCollectionsAreAllowed(): bool
//    {
//        $model = $this->resolveModel();
//
//        if (! $model) {
//            return $this->isTemporaryUpload()
//                ? $this->temporaryRequestedCollectionsAreAllowed()
//                : false;
//        }
//
//        return $this->collectionsAreAllowed(
//            $this->requestedCollectionNames(),
//            $model->allowedMediaCollections()
//        );
//    }

//    protected function temporaryRequestedCollectionsAreAllowed(): bool
//    {
//        $modelClass = $this->resolveModelClass();
//
//        if (! $modelClass) {
//            return false;
//        }
//
//        $model = new $modelClass;
//
//        if (! $model instanceof HasMediaExtended) {
//            return false;
//        }
//
//        return $this->collectionsAreAllowed(
//            $this->requestedCollectionNames(),
//            $model->allowedMediaCollections()
//        );
//    }

//    protected function requestedCollectionNames(): array
//    {
//        $collections = [];
//
//        if ($this->has('collections')) {
//            $inputCollections = $this->input('collections');
//
//            if (is_string($inputCollections)) {
//                $decodedCollections = json_decode($inputCollections, true);
//                $inputCollections = is_array($decodedCollections) ? $decodedCollections : [];
//            }
//
//            if (is_array($inputCollections)) {
//                $collections = array_merge($collections, $this->flattenCollectionNames($inputCollections));
//            }
//        }
//
//        if ($this->filled('collection')) {
//            $collections[] = (string) $this->input('collection');
//        }
//
//        if ($this->filled('target_media_collection')) {
//            $collections[] = (string) $this->input('target_media_collection');
//        }
//
//        return array_values(array_unique(array_filter($collections)));
//    }

//    protected function flattenCollectionNames(array $collections): array
//    {
//        $names = [];
//
//        foreach ($collections as $key => $value) {
//            if (is_string($value)) {
//                $names[] = $value;
//
//                continue;
//            }
//
//            if (is_array($value)) {
//                $names = array_merge($names, $this->flattenCollectionNames($value));
//            }
//        }
//
//        return $names;
//    }

//    protected function collectionsAreAllowed(array $requestedCollections, array $allowedCollections): bool
//    {
//        $requestedCollections = array_values(array_unique(array_filter($requestedCollections)));
//        $allowedCollections = array_values(array_unique(array_filter($allowedCollections)));
//
//        if ($requestedCollections === []) {
//            return true;
//        }
//
//        if ($allowedCollections === []) {
//            return true;
//        }
//
//        return empty(array_diff($requestedCollections, $allowedCollections));
//    }

    /**
     * Override the redirect URL to include the Base ID.
     */
    protected function getRedirectUrl(): string
    {
        $url = parent::getRedirectUrl();
        $baseId = $this->input('base_id');

        if ($baseId) {
            $url .= '#'.$baseId;
        }

        return $url;
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        $response = MediaResponse::error(
            $this,
            $this->input('base_id') ?? 'unknown',
            $errors->first(),
            [
                'errors' => $errors->messages(),
            ]
        );

        $response->setStatusCode(422);

        throw new ValidationException($validator, $response);
    }

    public function prepareForValidation(): void
    {
        $dataSource = $this->input('data_source');

        if (
            $dataSource === null ||
            (is_string($dataSource) && trim($dataSource) === '') ||
            $dataSource === 'null' ||
            $dataSource === 'undefined'
        ) {
            $this->merge([
                'data_source' => 'default',
            ]);
        }
    }

    protected function passedValidation(): void
    {
        //
    }

    protected function abortWithMediaError(string $message, int $status): never
    {
        $response = MediaResponse::error(
            $this,
            $this->input('base_id') ?? 'unknown',
            $message,
        );

        $response->setStatusCode($status);

        throw new HttpResponseException($response);
    }

}
