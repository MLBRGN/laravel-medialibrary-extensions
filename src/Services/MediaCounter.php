<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;

class MediaCounter
{
    public function __construct(
        protected DataSourceResolver $dataSourceResolver,
    )
    {
    }

    // -------------------------------------------------------------------------
    // MEDIA COUNTING
    // -------------------------------------------------------------------------
    //
    // Goal:
    //
    // Count media regardless of whether it exists as:
    //
    // - permanent media
    // - temporary uploads
    //
    // Consider introducing a common abstraction so callers do not need to know
    // which storage type is being counted.
    public function countModelMediaInCollections(HasMedia $model, array $collections, ?string $dataSource): int
    {
        $connection = $this->dataSourceResolver->resolveConnection($dataSource);

        if (method_exists($model, 'setConnection') && $model->getConnectionName() !== $connection) {
            $model->setConnection($connection);
        }

        $count = collect($collections)
            ->filter(fn($collectionName) => !empty($collectionName))
            ->reduce(function (int $total, string $collectionName) use ($model) {
                $count = $model->getMedia($collectionName)->count();

                return $total + $count;
            }, 0);

        return $count;
    }

    /**
     * Count total temporary uploads for the current client and component instance in given collections.
     */
    public function countTemporaryUploadsInCollections(array $collections, string $instanceId = null, string $clientToken = null, string $dataSource = null): int
    {
        $collections = collect($collections)
            ->filter(fn($collectionName) => !empty($collectionName))
            ->values();

        $total = 0;

        foreach ($collections as $collectionName) {
            $items = TemporaryUpload::getForCurrentClient($collectionName, $instanceId, $dataSource, $clientToken);
            $c = $items->count();

//            Log::debug('mle.countTemporaryUploadsInCollections.per_collection', [
//                'collection' => $collectionName,
//                'count' => $c,
//                'instanceId' => $instanceId,
//                'dataSource' => $dataSource,
//                'clientToken' => $clientToken ? substr($clientToken, 0, 4).'…'.substr($clientToken, -4) : null,
//            ]);

            $total += $c;
        }

//        Log::debug('mle.countTemporaryUploadsInCollections.total', [
//            'total' => $total,
//            'collections' => $collections->all(),
//            'instanceId' => $instanceId,
//            'dataSource' => $dataSource,
//            'clientToken' => $clientToken ? substr($clientToken, 0, 4).'…'.substr($clientToken, -4) : null,
//        ]);

        return $total;
    }

    // TODO This is the high-level counting API.
    //
    // Prefer callers using this method instead of directly calling:
    //
    // - countModelMediaInCollections()
    // - countTemporaryUploadsInCollections()
    //
    // The lower-level methods should become implementation details.
    public function countMediaInCollections(
        ResolvedModel $resolvedModel,
        array         $collections,
        ?string       $instanceId = null,
        ?string       $clientToken = null,
        ?string       $dataSource = null,
    ): int
    {
        if (!$resolvedModel->temporaryUploadMode) {
            return $this->countModelMediaInCollections(
                $resolvedModel->model,
                $collections,
                $dataSource
            );
        }

        if ($instanceId === null || $clientToken === null || $dataSource === null) {
            throw new \InvalidArgumentException('instanceId, clientToken, and dataSource are required when using temporary uploads');
        }

        return $this->countTemporaryUploadsInCollections(
            $collections,
            $instanceId,
            $clientToken,
            $dataSource
        );
    }

    /**
     * Check if a model already has any media in the given collections (single-media limit).
     */
    public function modelHasAnyMedia(HasMediaExtended $model, array $collections, ?string $dataSource): bool
    {
        return $this->countModelMediaInCollections($model, $collections, $dataSource) > 0;
    }

    /**
     * Check if there are temporary uploads in the given collections (single-media limit).
     */
    public function temporaryUploadsHaveAnyMedia(array $collections, ?string $instanceId = null, ?string $clientToken = null, ?string $dataSource): bool
    {
        return $this->countTemporaryUploadsInCollections($collections, $instanceId, $clientToken, $dataSource) > 0;
    }
}
