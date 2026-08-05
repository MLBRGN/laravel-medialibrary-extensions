<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
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

    // -------------------------------------------------------------------------
    // MODEL RESOLUTION
    // -------------------------------------------------------------------------
    //
    // Goal:
    // This section should become the single place responsible for:
    //
    // - resolving morph aliases to model classes
    // - validating HasMediaExtended
    // - creating model instances
    // - loading models from the database
    // - assigning database connections
    //
    //  TODO After refactoring, Requests and Rules should no longer perform any model
    // resolution themselves.
    public function resolveModelReference(Model|string $modelReference, ?string $dataSource): ResolvedModel
    {
        if ($modelReference instanceof HasMediaExtended) {
            return new ResolvedModel(
                model: $modelReference->setConnection($this->dataSourceResolver->resolveConnection($dataSource)),
                modelType: $modelReference->getMorphClass(),
                modelId: $modelReference->getKey(),
                temporaryUploadMode: false
            );
        } elseif (is_string($modelReference)) {
            if (!class_exists($modelReference)) {
                throw new InvalidArgumentException(__('medialibrary-extensions::messages.class_not_found', [
                    'class' => $modelReference,
                ]));
            }

            if (!is_subclass_of($modelReference, HasMediaExtended::class)) {
                throw new UnexpectedValueException(__('medialibrary-extensions::messages.must_implement_has_media', [
                    'class' => $modelReference,
                    'interface' => HasMediaExtended::class,
                ]));
            }

            return new ResolvedModel(
                model: null,
                modelType: $modelReference,
                modelId: null,
                temporaryUploadMode: true
            );
        } else {
            throw new \TypeError('model-or-class-name must be either a HasMedia model or a string representing the model class');
        }
    }

    public function instantiateTemporaryUpload(
        ?string $dataSource
    ): HasMediaExtended
    {

        $connection = $this->dataSourceResolver
            ->resolveConnection($dataSource);

        $model = new TemporaryUpload;
        $model->setConnection($connection);

        return $model;
    }

    /*
    * Use this method to resolve a model by its ID.
    * also sets the correct connection for the model.
    */
    // TODO This should become the primary model lookup API.
    //
    // This method currently:
    //
    // - validates the class
    // - validates HasMediaExtended
    // - creates a model
    // - assigns the connection
    // - loads the database record
    //
    // Consider extracting the common setup logic shared with make() so there is
    // only one place responsible for:
    //
    // - validating model classes
    // - assigning connections
    public function resolveModelById(
        ?string         $modelClass,
        string|int|null $id,
        ?string         $dataSource,
        bool            $validateExtended = true
    ): ?object
    {

        if ($modelClass === null || $id === null || $id === '' || (is_int($id) && $id <= 0)) {
            return null;
        }

        if (!class_exists($modelClass)) {
            throw InvalidModelTypeException::for($modelClass);
        }

        if ($validateExtended && $modelClass !== config('media-library.media_model')) {
            if (!is_subclass_of($modelClass, HasMediaExtended::class)) {
                throw InvalidModelTypeException::missingInterface($modelClass);
            }
        }

        $model = new $modelClass;

        $connection = $this->dataSourceResolver
            ->resolveConnection($dataSource);

        return $model
            ->setConnection($connection)
            ->newQuery()
            ->findOrFail($id);
    }

    /*
   * Use this method to find a medium by its ID.
   */
    // TODO These are convenience wrappers around resolveModelById().
    //
    // If additional wrapper methods are added in the future,
    // keep this section together so MediaService exposes a consistent lookup API.
    public function findMedium(
        string|int $id,
        ?string    $dataSource
    ): ?Media
    {
        return $this->resolveModelById(
            config('media-library.media_model'),
            $id,
            $dataSource,
            false
        );
    }

    /*
   * Use this method to find a temporary upload by its ID.
   */
    // TODO These are convenience wrappers around resolveModelById().
    //
    // If additional wrapper methods are added in the future,
    // keep this section together so MediaService exposes a consistent lookup API.
    public function findTemporaryUpload(
        string|int $id,
        ?string    $dataSource
    ): ?TemporaryUpload
    {
        return $this->resolveModelById(
            TemporaryUpload::class,
            $id,
            $dataSource
        );
    }

    public function resolveModelClass(string $modelType): ?string
    {
        if (class_exists(Relation::class)) {
            $modelType = Relation::getMorphedModel($modelType) ?? $modelType;
        }

        if (! class_exists($modelType)) {
            return null;
        }

        if (! is_subclass_of($modelType, HasMediaExtended::class)) {
            return null;
        }

        return $modelType;
    }

//    public function resolveRequestModel(
//        string $modelType,
//        string|int|null $modelId,
//        bool $temporaryUploadMode,
//        ?string $dataSource,
//    ): ?HasMediaExtended {
//
//        return null;
//    }
}
