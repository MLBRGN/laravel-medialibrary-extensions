<?php

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Spatie\MediaLibrary\HasMedia;

trait ChecksMediaLimits
{
    /**
     * Count total media for a model in given collections.
     */
    protected function countModelMediaInCollections(HasMedia $model, array $collections, ?string $dataSource = null): int
    {
        return app(MediaService::class)->countModelMediaInCollections($model, $collections, $dataSource);
    }

    /**
     * Count total temporary uploads for current session in given collections.
     */
    protected function countTemporaryUploadsInCollections(array $collections, ?string $instanceId = null, ?string $sessionId = null, ?string $dataSource = null): int
    {
        return app(MediaService::class)->countTemporaryUploadsInCollections($collections, $instanceId, $sessionId, $dataSource);
    }

    /**
     * Check if a model already has any media in the given collections (single-media limit).
     */
    protected function modelHasAnyMedia(HasMedia $model, array $collections, ?string $dataSource = null): bool
    {
        return $this->countModelMediaInCollections($model, $collections, $dataSource) > 0;
    }

    /**
     * Check if there are temporary uploads in the given collections (single-media limit).
     */
    protected function temporaryUploadsHaveAnyMedia(array $collections, ?string $instanceId = null, ?string $sessionId = null, ?string $dataSource = null): bool
    {
        return $this->countTemporaryUploadsInCollections($collections, $instanceId, $sessionId, $dataSource) > 0;
    }
}
