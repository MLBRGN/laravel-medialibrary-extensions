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
//    protected function resolveMediaFromCollections(array $collections, $instanceId): MediaCollection
//    {
//        // CASE 1: If a single medium is provided, use only that.
//        if (isset($this->singleMedia) && ($this->singleMedia instanceof Media || $this->singleMedia instanceof TemporaryUpload)) {
//            return MediaCollection::make(collect([$this->singleMedia]));
//        }
//
//        // CASE 2: Collect from all configured collections.
//        $media = collect($collections)
//            ->filter(fn ($collectionName) => ! empty($collectionName))
//            ->flatMap(function ($collectionNames, string $collectionType) use ($instanceId) {
//                // Normalize into array for uniform handling
//                $collectionNames = is_array($collectionNames)
//                    ? $collectionNames
//                    : [$collectionNames];
//
//                return collect($collectionNames)
//                    ->flatMap(function ($collectionName) use ($instanceId) {
//                        if ($this->temporaryUploadMode ?? false) {
//                            return TemporaryUpload::getForCurrentClient($collectionName, $instanceId, null, $this->clientToken);
//                        }
//
//                        if (isset($this->model) && $this->model) {
//                            return $this->model->getMedia($collectionName);
//                        }
//
//                        return [];
//                    });
//            })
//            ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
//            ->values();
//
//        return MediaCollection::make($media);
//    }

    protected function resolveMediaFromCollections(array $collections, $instanceId): MediaCollection
    {
        dump('$collections ' . json_encode($collections) . ' $instanceId ' . $instanceId);
        $modelConnection = $this->model?->getConnectionName();

        dump($modelConnection);
        $media = collect($collections)
            ->filter(fn ($collectionName) => ! empty($collectionName))
            ->flatMap(function ($collectionNames, string $collectionType) use ($instanceId, $modelConnection) {

                $collectionNames = is_array($collectionNames)
                    ? $collectionNames
                    : [$collectionNames];

                return collect($collectionNames)
                    ->flatMap(function ($collectionName) use ($instanceId, $modelConnection) {

                        if ($this->temporaryUploadMode ?? false) {
                            return TemporaryUpload::getForCurrentClient(
                                $collectionName,
                                $instanceId,
                                $modelConnection, // or pass explicitly if supported
                                $this->clientToken
                            );
                        }

                        if ($this->model) {
                            // IMPORTANT: ensure no accidental connection switching
                            return $this->model
                                ->setConnection($modelConnection)
                                ->getMedia($collectionName);
                        }

                        return [];
                    });
            })
            ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
            ->values();

        return MediaCollection::make($media);
    }
}
