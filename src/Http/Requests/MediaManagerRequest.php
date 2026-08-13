<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Mlbrgn\MediaLibraryExtensions\Helpers\MediaResponse;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Interfaces\MediaActionsAuthorizer;
use Mlbrgn\MediaLibraryExtensions\Services\MediaModelResolver;
use Throwable;

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
        'edit' => 'allowsMediaEdits',
        'delete' => 'allowsMediaDeletes',
    ];

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

    protected function authorizeMediaAction(string $ability): bool
    {
        $supportsMethod = self::ACTION_MAP[$ability] ?? null;

        if (! $supportsMethod) {
            return false;
        }

        if (! $this->resolveModelClassFromInput()) {
            return false;
        }

        if ($this->isTemporaryUpload() && in_array($ability, ['upload', 'edit'])) {
            return true;
        }

        $model = $this->resolveRequestModel();

        if (! $model) {
            return false;
        }

        if (! $model::{$supportsMethod}()) {
            return false;
        }

        return app(MediaActionsAuthorizer::class)
            ->allows(
                $ability,
                $this->user(),
                $model,
            );
    }

    protected function resolveRequestModel(): ?HasMediaExtended
    {
        return app(MediaModelResolver::class)->resolveRequestModel(
            modelType: $this->input('model_type'),
            modelId: $this->input('model_id'),
            dataSource: $this->input('data_source') ?? 'default',
        );
    }

    protected function resolveValidationModel(): ?HasMediaExtended
    {
        $model = $this->resolveRequestModel();

        if ($model) {
            return $model;
        }

        $modelClass = $this->resolveModelClassFromInput();

        if (! $modelClass) {
            return null;
        }

        return new $modelClass;
    }

    protected function resolveModelClassFromInput(): ?string
    {
        $modelType = $this->input('model_type');

        if (! is_string($modelType) || trim($modelType) === '') {
            return null;
        }

        try {
            return app(MediaModelResolver::class)->resolveModelClass($modelType);
        } catch (Throwable) {
            return null;
        }
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
