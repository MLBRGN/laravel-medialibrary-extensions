<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Interfaces\MediaActionsAuthorizer;
use Mlbrgn\MediaLibraryExtensions\Services\MediaModelResolver;
use UnexpectedValueException;

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

    protected const array ACTION_MAP = [
        'upload' => 'allowsMediaUploads',
        'edit'   => 'allowsMediaEdits',
        'delete' => 'allowsMediaDeletes',
    ];

    // TODO Refactor model resolution responsibilities.
    //
    // Goal:
    // - The Request should only know about request input and authorization.
    // - MediaModelResolver should own all model resolution logic.
    //
    // Steps:
    // 1. Move model_type -> model class resolution (including morph map support)
    //    into MediaModelResolver.
    // 2. Move model instantiation into MediaModelResolver.
    // 3. Move loading an existing model by ID into MediaModelResolver.
    // 4. Centralize exception handling (ModelNotFoundException,
    //    QueryException, etc.) inside MediaModelResolver.
    // 5. Let this Request simply ask MediaModelResolver for either:
    //      - an existing model, or
    //      - a new model instance (for temporary uploads).
    //
    // Desired end result:
    // $model = $MediaModelResolver->resolveRequestModel(...);
    // or
    // $model = $MediaModelResolver->makeRequestModel(...);
    //
    // The Request should no longer need to know about:
    // - Relation::getMorphedModel()
    // - class_exists()
    // - HasMediaExtended validation
    // - database connections
    // - model lookup exception handling


    // TODO Remove.
    // This should eventually delegate entirely to MediaModelResolver.
    protected function mediaModel(): ?HasMediaExtended
    {
        // return model if any
        if ($model = $this->resolveModel()) {
            return $model;
        }

        // return the model class name otherwise
        if ($modelClass = $this->resolveModelClass()) {
            return new $modelClass;
        }

        // return null, if both not found
        return null;
    }

    protected function resolveModelClass(): ?string
    {
        try {
            return app(MediaModelResolver::class)
                ->resolveModelClass($this->input('model_type'));
        } catch (InvalidArgumentException|UnexpectedValueException) {
            return null;
        }

    }

    protected function resolveModel(): ?HasMediaExtended
    {
        if ($this->isTemporaryUpload()) {
            return null;
        }

        $modelType = $this->input('model_type');
        $modelId = $this->input('model_id');
        $dataSource = $this->input('data_source') ?? 'default';

        return app(MediaModelResolver::class)->resolveRequestModel(
            modelType: $modelType,
            modelId: $modelId,
            dataSource: $dataSource,
        );
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

    // TODO After model resolution has moved to MediaModelResolver, this method should
    // only contain authorization logic:
    //
    // 1. Allow temporary upload abilities where appropriate.
    // 2. Ask MediaModelResolver for the model.
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

        if (! $model) {
            return false;
        }

        $supportsMethod = self::ACTION_MAP[$ability] ?? null;

        if (! $supportsMethod) {
            return false;
        }

        if (! $model::{$supportsMethod}()) {
//        if (! $model::$supportsMethod()) {
            return false;
        }

        return app(MediaActionsAuthorizer::class)
            ->allows(
                $ability,
                $this->user(),
                $model,
            );

//        return $model?->canPerformMediaAction($ability, $this->user()) ?? false;
    }

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
