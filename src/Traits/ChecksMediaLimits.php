<?php

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;

trait ChecksMediaLimits
{
    /**
     * Count total media for a model in given collections.
     */
    protected function countModelMediaInCollections(HasMedia $model, array $collections): int
    {
        $count = collect($collections)
            ->filter(fn ($collectionName, $collectionType) => !empty($collectionName))
            ->reduce(function (int $total, string $collectionName) use ($model) {
                $count = $model->getMedia($collectionName)->count();
                return $total + $count;
            }, 0);

        return $count;
    }

    /**
     * Count total temporary uploads for current session in given collections.
     */
    protected function countTemporaryUploadsInCollections(array $collections): int
    {
        $count = collect($collections)
            ->filter(fn ($collectionName, $collectionType) => !empty($collectionName))
            ->reduce(function (int $total, string $collectionName) {
                $temporaryItems = TemporaryUpload::forCurrentSession($collectionName);
                return $total + $temporaryItems->count();
            }, 0);

        return $count;
    }

    /**
     * Check if a model already has any media in the given collections (single-medium limit).
     */
    protected function modelHasAnyMedia(HasMedia $model, array $collections): bool
    {
        return $this->countModelMediaInCollections($model, $collections) > 0;
    }

    /**
     * Check if there are temporary uploads in the given collections (single-medium limit).
     */
    protected function temporaryUploadsHaveAnyMedia(array $collections): bool
    {
        return $this->countTemporaryUploadsInCollections($collections) > 0;
    }
}
