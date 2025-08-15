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
        return collect($collections)
            ->filter() // remove null/empty
            ->reduce(fn(int $total, string $collection) => $total + $model->getMedia($collection)->count(), 0);
    }

    /**
     * Count total temporary uploads for current session in given collections.
     */
    protected function countTemporaryUploadsInCollections(array $collections): int
    {
        return collect($collections)
            ->filter()
            ->reduce(fn(int $total, string $collection) => $total + TemporaryUpload::forCurrentSession($collection)->count(), 0);
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
