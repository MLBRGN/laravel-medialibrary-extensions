<?php

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Mlbrgn\MediaLibraryExtensions\Services\MediaCounter;
use Mlbrgn\MediaLibraryExtensions\Services\ResolvedModel;
use Spatie\MediaLibrary\HasMedia;

trait ChecksMediaLimits
{
    /**
     * Count total media for a model in given collections.
     */
    protected function countModelMediaInCollections(HasMedia $model, array $collections, ?string $dataSource = 'default'): int
    {
        return app(MediaCounter::class)->countModelMediaInCollections($model, $collections, $dataSource);
    }

    /**
     * Count total temporary uploads for current client in given collections.
     */
    protected function countTemporaryUploadsInCollections(array $collections, ?string $instanceId = null, ?string $clientToken = null, ?string $dataSource = 'default'): int
    {
        return app(MediaCounter::class)->countTemporaryUploadsInCollections($collections, $instanceId, $clientToken, $dataSource);
    }

    /**
     * Get the effective media count for a given context.
     * This sums permanent media, temporary uploads, and direct uploads.
     */
    protected function getEffectiveMediaCount(
        array $collections,
        ResolvedModel|HasMedia|string|null $model = null,
        ?string $instanceId = null,
        ?string $clientToken = null,
        ?string $dataSource = 'default',
        mixed $value = null,
        bool $ignoreClientToken = false
    ): int {
        return app(MediaCounter::class)->getEffectiveMediaCount(
            $collections,
            $model,
            $instanceId,
            $clientToken,
            $dataSource,
            $value,
            $ignoreClientToken
        );
    }

    /**
     * Check if a model already has any media in the given collections (single-media limit).
     */
    protected function modelHasAnyMedia(HasMedia $model, array $collections, ?string $dataSource = 'default'): bool
    {
        $mediaCounter = app(MediaCounter::class);

        return $this->countModelMediaInCollections($model, $collections, $dataSource) > 0;
    }

    /**
     * Check if there are temporary uploads in the given collections (single-media limit).
     */
    //    protected function temporaryUploadsHaveAnyMedia(array $collections, ?string $instanceId = null, ?string $clientToken = null, ?string $dataSource = 'default'): bool
    protected function temporaryUploadsHaveAnyMedia(array $collections, ?string $instanceId = null, ?string $clientToken = null, ?string $dataSource = 'default'): bool
    {
        $mediaCounter = app(MediaCounter::class);

        return $this->countTemporaryUploadsInCollections($collections, $instanceId, $clientToken, $dataSource) > 0;
    }
}
