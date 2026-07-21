<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Mlbrgn\MediaLibraryExtensions\Exceptions\InvalidModelTypeException;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use UnexpectedValueException;

// TODO Refactor this service into clear responsibility groups.
//
// Current responsibilities:
// - Model resolution
// - Model creation
// - Database lookups
// - Media retrieval
// - Media counting
// - Collection helper utilities
//
// Goal:
// 1. Group all model resolution APIs together.
// 2. Use consistent naming for "resolve", "make" and "find".
// 3. Remove duplicated validation/connection logic.
// 4. Make MediaManagerRequest depend on this service for all model resolution.
// 5. Consider extracting media counting/retrieval into separate services if
//    MediaService continues to grow.

// Future design:
//
// High-level public APIs:
//
// resolveRequestModel(...)
// resolveModel(...)
// makeModel(...)
// resolveMedia(...)
// countMedia(...)
//
// Everything else should become private implementation details.
class MediaService
{
    public function __construct(
        protected DataSourceResolver $resolver
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
    // After refactoring, Requests and Rules should no longer perform any model
    // resolution themselves.

    // TODO Review this API.
    //
    // The current method mixes two concepts:
    //
    // - an existing model instance
    // - a model class name
    //
    // Consider splitting into explicit methods, for example:
    //
    // resolveModel(...)
    // makeModel(...)
    //
    // or introducing a dedicated RequestModelResolver.
    //
    // The current name is also difficult to understand because "resolve"
    // and "orClassName" describe different concerns.
    public function resolveModelOrClassName(Model|string $modelOrClassName, ?string $dataSource): ResolvedModel
    {
        if ($modelOrClassName instanceof HasMediaExtended) {
            return new ResolvedModel(
                model: $modelOrClassName->setConnection($this->resolver->resolveConnection($dataSource)),
                modelType: $modelOrClassName->getMorphClass(),
                modelId: $modelOrClassName->getKey(),
                temporaryUploadMode: false
            );
        } elseif (is_string($modelOrClassName)) {
            if (!class_exists($modelOrClassName)) {
                throw new InvalidArgumentException(__('medialibrary-extensions::messages.class_not_found', [
                    'class' => $modelOrClassName,
                ]));
            }

            if (!is_subclass_of($modelOrClassName, HasMediaExtended::class)) {
                throw new UnexpectedValueException(__('medialibrary-extensions::messages.must_implement_has_media', [
                    'class' => $modelOrClassName,
                    'interface' => HasMediaExtended::class,
                ]));
            }

            return new ResolvedModel(
                model: null,
                modelType: $modelOrClassName,
                modelId: null,
                temporaryUploadMode: true
            );
        } else {
            throw new \TypeError('model-or-class-name must be either a HasMedia model or a string representing the model class');
        }
    }

    // TODO Rename.
    //
    // "make()" is too generic.
    //
    // This method actually:
    //
    // - instantiates a model
    // - assigns the correct database connection
    //
    // Possible names:
    //
    // makeModel()
    // instantiateModel()
    // createModelInstance()
    //
    // It should probably return HasMediaExtended instead of object.
    public function make(
        string  $modelClass,
        ?string $dataSource
    ): object
    {

        $connection = $this->resolver
            ->resolveConnection($dataSource);

        $model = new $modelClass;
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

        $connection = $this->resolver->resolveConnection($dataSource);

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

    // TODO should this be in media service?
    public function determineCollectionType($file): ?string
    {
        $mimeType = $file->getMimeType();

        if (in_array($mimeType, config('medialibrary-extensions.allowed_mimetypes.image'))) {
            return 'image';
        }

        if (in_array($mimeType, config('medialibrary-extensions.allowed_mimetypes.document'))) {
            return 'document';
        }

        if (in_array($mimeType, config('medialibrary-extensions.allowed_mimetypes.audio'))) {
            return 'audio';
        }

        if (in_array($mimeType, config('medialibrary-extensions.allowed_mimetypes.video'))) {
            return 'video';
        }

        return null;
    }

    // -------------------------------------------------------------------------
    // MEDIA COUNTING
    // -------------------------------------------------------------------------
    //
    // Goal:
    //
    // Count media regardless of whether it exists as:
    //
    // - permanent media
    // - temporary uploads
    //
    // Consider introducing a common abstraction so callers do not need to know
    // which storage type is being counted.
    public function countModelMediaInCollections(HasMedia $model, array $collections, ?string $dataSource): int
    {
        $connection = $this->resolver->resolveConnection($dataSource);

        if (method_exists($model, 'setConnection') && $model->getConnectionName() !== $connection) {
            $model->setConnection($connection);
        }

        $count = collect($collections)
            ->filter(fn($collectionName) => !empty($collectionName))
            ->reduce(function (int $total, string $collectionName) use ($model) {
                $count = $model->getMedia($collectionName)->count();

                return $total + $count;
            }, 0);

        return $count;
    }

    /**
     * Count total temporary uploads for the current client and component instance in given collections.
     */
    public function countTemporaryUploadsInCollections(array $collections, string $instanceId = null, string $clientToken = null, string $dataSource = null): int
    {
        $collections = collect($collections)
            ->filter(fn($collectionName) => !empty($collectionName))
            ->values();

        $total = 0;

        foreach ($collections as $collectionName) {
            $items = TemporaryUpload::getForCurrentClient($collectionName, $instanceId, $dataSource, $clientToken);
            $c = $items->count();

//            Log::debug('mle.countTemporaryUploadsInCollections.per_collection', [
//                'collection' => $collectionName,
//                'count' => $c,
//                'instanceId' => $instanceId,
//                'dataSource' => $dataSource,
//                'clientToken' => $clientToken ? substr($clientToken, 0, 4).'…'.substr($clientToken, -4) : null,
//            ]);

            $total += $c;
        }

//        Log::debug('mle.countTemporaryUploadsInCollections.total', [
//            'total' => $total,
//            'collections' => $collections->all(),
//            'instanceId' => $instanceId,
//            'dataSource' => $dataSource,
//            'clientToken' => $clientToken ? substr($clientToken, 0, 4).'…'.substr($clientToken, -4) : null,
//        ]);

        return $total;
    }

    // TODO This is the high-level counting API.
    //
    // Prefer callers using this method instead of directly calling:
    //
    // - countModelMediaInCollections()
    // - countTemporaryUploadsInCollections()
    //
    // The lower-level methods should become implementation details.
    public function countMediaInCollections(
        ResolvedModel $resolvedModel,
        array         $collections,
        ?string       $instanceId = null,
        ?string       $clientToken = null,
        ?string       $dataSource = null,
    ): int
    {
        if (!$resolvedModel->temporaryUploadMode) {
            return $this->countModelMediaInCollections(
                $resolvedModel->model,
                $collections,
                $dataSource
            );
        }

        if ($instanceId === null || $clientToken === null || $dataSource === null) {
            throw new \InvalidArgumentException('instanceId, clientToken, and dataSource are required when using temporary uploads');
        }

        return $this->countTemporaryUploadsInCollections(
            $collections,
            $instanceId,
            $clientToken,
            $dataSource
        );
    }

    /**
     * Check if a model already has any media in the given collections (single-media limit).
     */
    public function modelHasAnyMedia(HasMediaExtended $model, array $collections, ?string $dataSource): bool
    {
        return $this->countModelMediaInCollections($model, $collections, $dataSource) > 0;
    }

    /**
     * Check if there are temporary uploads in the given collections (single-media limit).
     */
    public function temporaryUploadsHaveAnyMedia(array $collections, ?string $instanceId = null, ?string $clientToken = null, ?string $dataSource): bool
    {
        return $this->countTemporaryUploadsInCollections($collections, $instanceId, $clientToken, $dataSource) > 0;
    }

    // -------------------------------------------------------------------------
    // MEDIA RETRIEVAL
    // -------------------------------------------------------------------------
    //
    // Goal:
    //
    // Return media from:
    //
    // - permanent storage
    // - temporary uploads
    //
    // while hiding where the media actually comes from.

    // TODO This should become the preferred retrieval API.
    //
    // The helper methods below should remain private implementation details.
    public function resolveMediaFromCollections(
        ?Model  $model,
        array   $collections,
        ?string $instanceId,
        ?string $clientToken,
        ?string $dataSource,
        bool    $includeTemporaryUploads = false,
    ): MediaCollection
    {
        $media = collect();

        if ($model instanceof Model) {
            $media = $media->merge(
                $this->resolvePermanentMedia($model, $collections)
            );
        }

        if ($includeTemporaryUploads) {
            $media = $media->merge(
                $this->resolveTemporaryMedia(
                    $collections,
                    $instanceId,
                    $clientToken,
                    $dataSource,
                )
            );
        }

        return MediaCollection::make(
            $media
                ->sortBy(fn($media) => $media->getCustomProperty('priority', PHP_INT_MAX))
                ->values()
        );
    }

    private function resolvePermanentMedia(
        Model $model,
        array $collections,
    ): Collection
    {
        $connection = $model->getConnectionName();

        return $this->collectionNames($collections)
            ->flatMap(fn(string $collection) => $model
                ->setConnection($connection)
                ->getMedia($collection));
    }

    private function resolveTemporaryMedia(
        array   $collections,
        ?string $instanceId,
        ?string $clientToken,
        ?string $dataSource,
    ): Collection
    {
        return $this->collectionNames($collections)
            ->flatMap(fn(string $collection) => TemporaryUpload::getForCurrentClient(
                $collection,
                $instanceId,
                $dataSource,
                $clientToken,
            ));
    }

    public function getTemporaryUploadsSorted(
        array|string|null $collections = null,
        ?string           $instanceId = null,
        ?string           $clientToken = null,
        ?string           $dataSource = 'default',
    ): Collection
    {
        return TemporaryUpload::getForCurrentClient(
            $collections,
            $instanceId,
            $dataSource,
            $clientToken,
        )->sortBy(fn($upload) => $upload->getCustomProperty('priority', PHP_INT_MAX))
            ->values();
    }

    // TODO Consider extracting collection normalization into a reusable helper.
    //
    // Multiple methods in this service work with normalized collection names.
    // If additional collection logic is introduced, this may deserve its own
    // CollectionNameNormalizer.
    private function collectionNames(array $collections): Collection
    {
        return collect($collections)
            ->filter()
            ->flatMap(fn($names) => is_array($names) ? $names : [$names]);
    }
}
