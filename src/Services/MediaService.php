<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Mlbrgn\MediaLibraryExtensions\Exceptions\InvalidModelTypeException;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use UnexpectedValueException;

class MediaService
{
    public function __construct(
        protected DataSourceResolver $resolver
    ) {}

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
            if (! class_exists($modelOrClassName)) {
                throw new InvalidArgumentException(__('medialibrary-extensions::messages.class_not_found', [
                    'class' => $modelOrClassName,
                ]));
            }

            if (! is_subclass_of($modelOrClassName, HasMediaExtended::class)) {
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

    public function make(
        string $modelClass,
        ?string $dataSource
    ): object {

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
    public function resolveModelById(
        ?string $modelClass,
        string|int|null $id,
        ?string $dataSource,
        bool $validateExtended = true
    ): ?object {

        if ($modelClass === null || $id === null || $id === '' || (is_int($id) && $id <= 0)) {
            return null;
        }

        if (! class_exists($modelClass)) {
            throw InvalidModelTypeException::for($modelClass);
        }

        if ($validateExtended && $modelClass !== config('media-library.media_model')) {
            if (! is_subclass_of($modelClass, HasMediaExtended::class)) {
                throw InvalidModelTypeException::missingInterface($modelClass);
            }
        }

        $model = new $modelClass;

        $connection = $this->resolver->resolveConnection($dataSource);

        try {
            return $model
                ->setConnection($connection)
                ->newQuery()
                ->findOrFail($id);
        } catch (\Exception $e) {
            Log::error('MediaService - resolveModelById failed', [
                'id' => $id,
                'connection' => $connection,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /*
     * Use this method to find a medium by its ID.
     */
    public function findMedium(
        string|int $id,
        ?string $dataSource
    ): ?Media {
        try {
            return $this->resolveModelById(
                config('media-library.media_model'),
                $id,
                $dataSource,
                false
            );
        } catch (\Exception) {
            return null;
        }
    }

    /*
     * Use this method to find a temporary upload by its ID.
     */
    public function findTemporaryUpload(
        string|int $id,
        ?string $dataSource
    ): ?TemporaryUpload {
        try {
            return $this->resolveModelById(
                TemporaryUpload::class,
                $id,
                $dataSource
            );
        } catch (\Exception) {
            return null;
        }
    }

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

    public function countModelMediaInCollections(HasMedia $model, array $collections, ?string $dataSource): int
    {
        $connection = $this->resolver->resolveConnection($dataSource);

        if (method_exists($model, 'setConnection') && $model->getConnectionName() !== $connection) {
            $model->setConnection($connection);
        }

        $count = collect($collections)
            ->filter(fn ($collectionName) => ! empty($collectionName))
            ->reduce(function (int $total, string $collectionName) use ($model) {
                $count = $model->getMedia($collectionName)->count();

                return $total + $count;
            }, 0);

        return $count;
    }

    /**
     * Count total temporary uploads for the current client and component instance in given collections.
     */
//    public function countTemporaryUploadsInCollections(array $collections, ?string $instanceId = null, ?string $clientToken = null, ?string $dataSource): int
    public function countTemporaryUploadsInCollections(array $collections, string $instanceId = null, string $clientToken = null, string $dataSource = null): int
    {
        $collections = collect($collections)
            ->filter(fn ($collectionName) => ! empty($collectionName))
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

    public function countMediaInCollections(
        ResolvedModel $resolvedModel,
        array $collections,
        ?string $instanceId = null,
        ?string $clientToken = null,
        ?string $dataSource = null,
    ): int
    {
        if (! $resolvedModel->temporaryUploadMode) {
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

//    public function resolveMediaFromCollections(Model $model, array $collections, $instanceId, $dataSource): MediaCollection
//    {
//        dump('$collections ' . json_encode($collections));
//        dump('$instanceId ' . $instanceId);
//        dump('$dataSource ' . $dataSource);
//
//        $modelConnection = $model?->getConnectionName();
//
//        dump($modelConnection);
//        $media = collect($collections)
//            ->filter(fn ($collectionName) => ! empty($collectionName))
//            ->flatMap(function ($collectionNames, string $collectionType) use ($model, $instanceId, $modelConnection) {
//
//                $collectionNames = is_array($collectionNames)
//                    ? $collectionNames
//                    : [$collectionNames];
//
//                return collect($collectionNames)
//                    ->flatMap(function ($collectionName) use ($model, $instanceId, $modelConnection) {
//
//                        if ($this->temporaryUploadMode ?? false) {
//                            return TemporaryUpload::getForCurrentClient(
//                                $collectionName,
//                                $instanceId,
//                                $modelConnection, // or pass explicitly if supported
//                                $this->clientToken
//                            );
//                        }
//
//                        if ($model) {
//                            // IMPORTANT: ensure no accidental connection switching
//                            return $model
//                                ->setConnection($modelConnection)
//                                ->getMedia($collectionName);
//                        }
//
//                        return [];
//                    });
//            })
//            ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
//            ->values();
//
//        return MediaCollection::make($media);
//    }

    public function resolveMediaFromCollections(
        ?Model $model,
        array $collections,
               $instanceId,
               $clientToken,
               $dataSource
    ): MediaCollection {

        // CASE A: TEMPORARY MODE (NO MODEL)
        if (! $model instanceof Model) {
            $media = collect($collections)
                ->filter(fn ($collectionName) => ! empty($collectionName))
                ->flatMap(function ($collectionNames) use ($clientToken, $instanceId, $dataSource) {

                    $collectionNames = is_array($collectionNames)
                        ? $collectionNames
                        : [$collectionNames];

                    return collect($collectionNames)
                        ->flatMap(function ($collectionName) use ($clientToken, $instanceId, $dataSource) {
                            return TemporaryUpload::getForCurrentClient(
                                $collectionName,
                                $instanceId,
                                $dataSource,
                                $clientToken,
                            );
                        });
                })
                ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
                ->values();

            return MediaCollection::make($media);
        }

        // CASE B: PERMANENT MEDIA (MODEL EXISTS)
        $modelConnection = $model->getConnectionName();

        $media = collect($collections)
            ->filter(fn ($collectionName) => ! empty($collectionName))
            ->flatMap(function ($collectionNames) use ($model, $modelConnection) {

                $collectionNames = is_array($collectionNames)
                    ? $collectionNames
                    : [$collectionNames];

                return collect($collectionNames)
                    ->flatMap(function ($collectionName) use ($model, $modelConnection) {
                        return $model
                            ->setConnection($modelConnection)
                            ->getMedia($collectionName);
                    });
            })
            ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
            ->values();

        return MediaCollection::make($media);
    }
}
