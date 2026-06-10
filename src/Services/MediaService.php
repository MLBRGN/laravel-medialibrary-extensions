<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Mlbrgn\MediaLibraryExtensions\Exceptions\InvalidModelTypeException;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use UnexpectedValueException;

class MediaService
{
    public function __construct(
        protected DataSourceResolver $resolver
    ) {}

    public function resolveModelOrClassName(Model|string $modelOrClassName, ?string $dataSource = null): ResolvedModel
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
        ?string $dataSource = null
    ): object {

        $connection = $this->resolver
            ->resolveConnection($dataSource);

        $model = new $modelClass;
        $model->setConnection($connection);

        return $model;
    }

    /*
     * Use this method to findMediaModel a model by its ID.
     */
    public function findMediaModel(
        ?string $modelClass,
        string|int|null $id,
        ?string $dataSource = null,
        bool $validateExtended = true
    ): ?object {
        Log::info('findMediaModel: '.($modelClass ?? 'NULL').' '.($id ?? 'NULL'));
        if ($modelClass === null || $id === null || $id === '' || (is_int($id) && $id <= 0)) {
            return null;
        }

        if (! class_exists($modelClass)) {
            Log::info('throws Invalid model type: '.$modelClass);
            throw InvalidModelTypeException::for($modelClass);
        }

        if ($validateExtended && $modelClass !== config('media-library.media_model')) {
            if (! is_subclass_of($modelClass, HasMediaExtended::class)) {
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
     * Use this method to findMediaModel a medium by its ID.
     */
    public function findMedium(
        string|int $id,
        ?string $dataSource = null
    ): ?Media {
        try {
            return $this->findMediaModel(
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
     * Use this method to findMediaModel a temporary upload by its ID.
     */
    public function findTemporaryUpload(
        string|int $id,
        ?string $dataSource = null
    ): ?TemporaryUpload {
        try {
            return $this->findMediaModel(
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

    public function countModelMediaInCollections(HasMedia $model, array $collections, ?string $dataSource = null): int
    {
        $connection = $this->resolver->resolveConnection($dataSource);

        if (method_exists($model, 'setConnection') && $model->getConnectionName() !== $connection) {
            $model->setConnection($connection);
        }

        $count = collect($collections)
            ->filter(fn ($collectionName, $collectionType) => ! empty($collectionName))
            ->reduce(function (int $total, string $collectionName) use ($model) {
                $count = $model->getMedia($collectionName)->count();

                return $total + $count;
            }, 0);

        return $count;
    }

    //
    //    /**
    //     * Count total temporary uploads for current session in given collections.
    //     */
    public function countTemporaryUploadsInCollections(array $collections, ?string $instanceId = null, ?string $sessionId = null, ?string $dataSource = null): int
    {
        $count = collect($collections)
            ->filter(fn ($collectionName, $collectionType) => ! empty($collectionName))
            ->reduce(function (int $total, string $collectionName) use ($instanceId, $sessionId, $dataSource) {
                $temporaryItems = TemporaryUpload::getForCurrentSession($collectionName, $instanceId, $dataSource, $sessionId);

                return $total + $temporaryItems->count();
            }, 0);

        return $count;
    }
    //
    //    /**
    //     * Check if a model already has any media in the given collections (single-media limit).
    //     */
    //    protected function modelHasAnyMedia(HasMediaExtended $model, array $collections, ?string $dataSource = null): bool
    //    {
    //        return $this->countModelMediaInCollections($model, $collections, $dataSource) > 0;
    //    }
    //
    //    /**
    //     * Check if there are temporary uploads in the given collections (single-media limit).
    //     */
    //    protected function temporaryUploadsHaveAnyMedia(array $collections, ?string $instanceId = null, ?string $sessionId = null, ?string $dataSource = null): bool
    //    {
    //        return $this->countTemporaryUploadsInCollections($collections, $instanceId, $sessionId, $dataSource) > 0;
    //    }
}
