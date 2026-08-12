<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Mlbrgn\MediaLibraryExtensions\Exceptions\InvalidModelTypeException;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use UnexpectedValueException;

class MediaModelResolver
{
    public function __construct(
        protected DataSourceResolver $dataSourceResolver,
    )
    {
    }

    public function resolveModelReference(HasMediaExtended|string $modelReference, ?string $dataSource): ResolvedModel
    {
        if ($modelReference instanceof HasMediaExtended) {
            return new ResolvedModel(
                model: $modelReference->setConnection(
                    $this->dataSourceResolver->resolveConnection($dataSource)
                ),
                modelType: $modelReference->getMorphClass(),
                modelId: $modelReference->getKey(),
                temporaryUploadMode: false
            );
        }

        $modelClass = $this->resolveModelClass($modelReference);

        return new ResolvedModel(
            model: null,
            modelType: $modelClass,
            modelId: null,
            temporaryUploadMode: true,
        );
    }

    public function instantiateTemporaryUpload(
        ?string $dataSource
    ): TemporaryUpload {
        return (new TemporaryUpload)->setConnection(
            $this->dataSourceResolver->resolveConnection($dataSource)
        );
    }

    public function resolveMediumById(
        ?string $modelClass,
        string|int|null $id,
        ?string $dataSource,
    ): Media|TemporaryUpload|null
    {
        if ($modelClass === null || $id === null || $id === '' || (is_int($id) && $id <= 0)) {
            return null;
        }

        if (!class_exists($modelClass)) {
            throw InvalidModelTypeException::for($modelClass);
        }

        if (
            $modelClass !== config('media-library.media_model')
            && $modelClass !== TemporaryUpload::class
        ) {
            throw InvalidModelTypeException::for($modelClass);
        }

        /** @var Media|TemporaryUpload */
        return $this->findById($modelClass, $id, $dataSource);
    }

    public function resolveModelById(
        ?string $modelClass,
        string|int|null $id,
        ?string $dataSource,
    ): ?HasMediaExtended
    {
        if ($modelClass === null || $id === null || $id === '' || (is_int($id) && $id <= 0)) {
            return null;
        }

        if (!class_exists($modelClass)) {
            throw InvalidModelTypeException::for($modelClass);
        }

        if (!is_subclass_of($modelClass, HasMediaExtended::class)) {
            throw InvalidModelTypeException::missingInterface($modelClass);
        }

        /** @var HasMediaExtended */
        return $this->findById($modelClass, $id, $dataSource);
    }


    private function findById(
        string $modelClass,
        string|int $id,
        ?string $dataSource,
    ): Model
    {
        $model = new $modelClass;

        return $model
            ->setConnection($this->dataSourceResolver->resolveConnection($dataSource))
            ->newQuery()
            ->findOrFail($id);
    }

    // Use this method to find a medium by its ID.
    public function findMedium(
        string|int $id,
        ?string    $dataSource
    ): ?Media
    {
        return $this->resolveMediumById(
            config('media-library.media_model'),
            $id,
            $dataSource
        );
    }

    // Use this method to find a temporary upload by its ID.
    public function findTemporaryUpload(
        string|int $id,
        ?string    $dataSource
    ): ?TemporaryUpload
    {
        return $this->resolveMediumById(
            TemporaryUpload::class,
            $id,
            $dataSource
        );
    }

    // Resolve a model class or morph alias to its fully qualified class name.
    public function resolveModelClass(string $modelType): string
    {
        // first look in morph map
        if (class_exists(Relation::class)) {
            $modelType = Relation::getMorphedModel($modelType) ?? $modelType;
        }

        if (! class_exists($modelType)) {
            throw new InvalidArgumentException(
                __('medialibrary-extensions::messages.class_not_found', [
                    'class' => $modelType,
                ])
            );
        }

        if (! is_subclass_of($modelType, HasMediaExtended::class)) {
            throw new UnexpectedValueException(
                __('medialibrary-extensions::messages.must_implement_has_media', [
                    'class' => $modelType,
                    'interface' => HasMediaExtended::class,
                ])
            );
        }

        return $modelType;
    }

    public function resolveRequestModel(
        ?string $modelType,
        string|int|null $modelId,
        ?string $dataSource,
    ): ?HasMediaExtended {
        if ($modelType === null || $modelType === '') {
            return null;
        }

        try {
            $modelClass = $this->resolveModelClass($modelType);
        } catch (InvalidArgumentException|UnexpectedValueException) {
            return null;
        }

        if ($modelId === null || $modelId === '') {
            return null;
        }

        try {
            /** @var HasMediaExtended $model */
            return $this->findById(
                $modelClass,
                $modelId,
                $dataSource,
            );
        } catch (ModelNotFoundException $e) {
            Log::warning(
                'Failed to resolve media model during request processing.',
                [
                    'model_type' => $modelType,
                    'model_id' => $modelId,
                    'data_source' => $dataSource,
                    'exception' => $e,
                ]
            );

            return null;
        } catch (QueryException $e) {
            Log::error(
                'Database query error while resolving media model: '.$e->getMessage(),
                [
                    'model_type' => $modelType,
                    'model_id' => $modelId,
                    'data_source' => $dataSource,
                ]
            );

            return null;
        }
    }
}
