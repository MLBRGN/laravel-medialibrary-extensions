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
        //        $model->load(['media' => fn($q) => $q->whereIn('collection_name', $collections)]);

        $count = collect($collections)
            ->filter()// remove falsy values
            ->reduce(function (int $total, string $collection) use ($model) {
                $mediaItems = $model->getMedia($collection);

                //                Log::info("Media for collection '{$collection}' count: " . $mediaItems->count());
                return $total + $mediaItems->count();
            }, 0);

        //        Log::info("Total count for collections '{$count}'");
        return $count;
    }

    /**
     * Count total temporary uploads for current session in given collections.
     */
    protected function countTemporaryUploadsInCollections(array $collections): int
    {
        $count = collect($collections)
            ->filter()// remove falsy values
            ->reduce(function (int $total, string $collection) {
                $temporaryItems = TemporaryUpload::forCurrentSession($collection);

                //                Log::info("Temporary items for collection '{$collection}' count: " . $temporaryItems->count());
                return $total + $temporaryItems->count();
            }, 0);
        //        Log::info("Total count for collections '{$count}'");

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
