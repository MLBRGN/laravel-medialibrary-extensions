<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class MediaRetriever
{
    public function __construct(
        protected DataSourceResolver $dataSourceResolver,
    ) {}

    public function resolveMediaFromCollections(
        ?Model $model,
        array $collections,
        ?string $instanceId,
        ?string $clientToken,
        ?string $dataSource,
        bool $includeTemporaryUploads = false,
    ): MediaCollection {
        $media = collect();

        if ($model instanceof Model) {
            $media = $media->merge(
                $this->resolvePermanentMedia($model, $collections)
            );
        }

        if ($includeTemporaryUploads) {
            $media = $media->merge(
                $this->resolveTemporaryMedia(
                    $collections,
                    $instanceId,
                    $clientToken,
                    $dataSource,
                )
            );
        }

        return MediaCollection::make(
            $media
                ->sortBy(fn ($media) => $media->getCustomProperty('priority', PHP_INT_MAX))
                ->values()
        );
    }

    private function resolvePermanentMedia(
        Model $model,
        array $collections,
    ): Collection {
        $connection = $model->getConnectionName();

        return $this->collectionNames($collections)
            ->flatMap(fn (string $collection) => $model
                ->setConnection($connection)
                ->getMedia($collection));
    }

    private function resolveTemporaryMedia(
        array $collections,
        ?string $instanceId,
        ?string $clientToken,
        ?string $dataSource,
    ): Collection {
        return $this->collectionNames($collections)
            ->flatMap(fn (string $collection) => TemporaryUpload::getForCurrentClient(
                $collection,
                $instanceId,
                $dataSource,
                $clientToken,
            ));
    }

    public function getTemporaryUploadsSorted(
        array|string|null $collections = null,
        ?string $instanceId = null,
        ?string $clientToken = null,
        ?string $dataSource = 'default',
    ): Collection {
        return TemporaryUpload::getForCurrentClient(
            $collections,
            $instanceId,
            $dataSource,
            $clientToken,
        )->sortBy(fn ($upload) => $upload->getCustomProperty('priority', PHP_INT_MAX))
            ->values();
    }

    // Multiple methods in this service work with normalized collection names.
    // If additional collection logic is introduced, this may deserve its own
    // CollectionNameNormalizer.
    private function collectionNames(array $collections): Collection
    {
        return collect($collections)
            ->filter()
            ->flatMap(fn ($names) => is_array($names) ? $names : [$names]);
    }
}
