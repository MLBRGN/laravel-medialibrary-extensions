<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Illuminate\Database\Eloquent\Model;

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
