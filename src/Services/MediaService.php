<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Illuminate\Database\Eloquent\Model;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaService
{
    public function __construct(
        protected MediaModelResolver $mediaModelResolver,
    )
    {
    }

    public function resolveModelReference(Model|string $modelReference, ?string $dataSource): ResolvedModel
    {
       return $this->mediaModelResolver->resolveModelReference($modelReference, $dataSource);
    }

    public function resolveModelById(
        ?string         $modelClass,
        string|int|null $id,
        ?string         $dataSource,
        bool            $validateExtended = true
    ): ?object
    {
        return $this->mediaModelResolver->resolveModelById(
            $modelClass,
            $id,
            $dataSource,
            $validateExtended,
        );
    }

    public function findMedium(
        string|int $id,
        ?string    $dataSource
    ): ?Media
    {
        return $this->mediaModelResolver->findMedium($id, $dataSource, false);
    }


    public function findTemporaryUpload(
        string|int $id,
        ?string    $dataSource
    ): ?TemporaryUpload
    {
        return $this->mediaModelResolver->findTemporaryUpload($id, $dataSource);
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
