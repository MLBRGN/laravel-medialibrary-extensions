<?php

namespace Mlbrgn\MediaLibraryExtensions\Traits;

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait InteractsWithMediaCollections
{
    /**
     * Load and merge media (or temporary uploads) from given collections.
     */
    protected function resolveMediaFromCollections(array $collections): MediaCollection
    {
        // CASE 1: If a single medium is provided, use only that.
        if (isset($this->singleMedium) && ($this->singleMedium instanceof Media || $this->singleMedium instanceof TemporaryUpload)) {
            return MediaCollection::make(collect([$this->singleMedium]));
        }

        // CASE 2: Collect from all configured collections.
        $media = collect($collections)
            ->filter(fn ($collectionName) => ! empty($collectionName))
            ->flatMap(function ($collectionNames, string $collectionType) {
                // Normalize into array for uniform handling
                $collectionNames = is_array($collectionNames)
                    ? $collectionNames
                    : [$collectionNames];

                return collect($collectionNames)
                    ->flatMap(function ($collectionName) {
                        if ($this->temporaryUploadMode ?? false) {
                            return TemporaryUpload::forCurrentSession($collectionName);
                        }

                        if (isset($this->model) && $this->model) {
                            return $this->model->getMedia($collectionName);
                        }

                        return [];
                    });
            })
            ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
            ->values();

        return MediaCollection::make($media);
    }
}
