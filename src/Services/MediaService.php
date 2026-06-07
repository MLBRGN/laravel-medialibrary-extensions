<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Mlbrgn\MediaLibraryExtensions\Exceptions\InvalidModelTypeException;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService
{
    public function __construct(
        protected DataSourceResolver $resolver
    ) {}

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
        string $modelClass,
        string|int|null $id,
        ?string $dataSource = null,
        bool $validateExtended = true
    ): ?object {
        if ($id === null || $id === '' || (is_int($id) && $id <= 0)) {
            return null;
        }

        if (! class_exists($modelClass)) {
            throw InvalidModelTypeException::for($modelClass);
        }

        if ($validateExtended && $modelClass !== config('media-library.media_model')) {
            if (! is_subclass_of($modelClass, HasMediaExtended::class)) {
                throw InvalidModelTypeException::missingInterface($modelClass);
            }

            $traits = class_uses_recursive($modelClass);
            if (! isset($traits[InteractsWithMediaExtended::class])) {
                throw InvalidModelTypeException::missingTrait($modelClass);
            }
        }

        $model = $this->make(
            $modelClass,
            $dataSource
        );

        return $model->newQuery()
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
}
