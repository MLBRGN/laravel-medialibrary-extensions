<?php

namespace Mlbrgn\MediaLibraryExtensions\Services;

use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;

class MediaCounter
{
    public function __construct(
        protected DataSourceResolver $dataSourceResolver,
        protected MediaModelResolver $modelResolver,
    ) {}

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
            ->filter(fn ($collectionName) => ! empty($collectionName))
            ->reduce(function (int $total, string $collectionName) use ($model) {
                $count = $model->getMedia($collectionName)->count();

                return $total + $count;
            }, 0);

        return $count;
    }

    /**
     * Count total temporary uploads for the current client and component instance in given collections.
     */
    public function countTemporaryUploadsInCollections(array $collections, ?string $instanceId = null, ?string $clientToken = null, ?string $dataSource = null, bool $ignoreClientToken = false): int
    {
        $collections = collect($collections)
            ->filter(fn ($collectionName) => ! empty($collectionName))
            ->values();

        if ($collections->isEmpty()) {
            return 0;
        }

        $query = TemporaryUpload::query()
            ->forDataSource($dataSource)
            ->forCollections($collections->all())
            ->forInstance($instanceId);

        if (! $ignoreClientToken) {
            $query->forCurrentClient($clientToken);
        }

        return $query->count();
    }

    /**
     * Get the effective media count for a given context.
     * This sums permanent media, temporary uploads, and direct uploads.
     */
    public function getEffectiveMediaCount(
        array $collections,
        ResolvedModel|HasMedia|string|null $model = null,
        ?string $instanceId = null,
        ?string $clientToken = null,
        ?string $dataSource = 'default',
        mixed $value = null,
        bool $ignoreClientToken = false
    ): int {
        $count = 0;

        // 1. Resolve model if needed
        $resolvedModel = null;
        if ($model instanceof ResolvedModel) {
            $resolvedModel = $model;
        } elseif ($model !== null) {
            $resolvedModel = $this->modelResolver->resolveModelReference($model, $dataSource);
        }

        // 2. Count permanent media
        if ($resolvedModel && $resolvedModel->model) {
            $count += $this->countModelMediaInCollections($resolvedModel->model, $collections, $dataSource);
        }

        // 3. Count temporary uploads
        // Some actions (like StoreMultipleTemporaryAction) require ignoring client_token
        // to enforce global capacity limits per instance.
        $count += $this->countTemporaryUploadsInCollections($collections, $instanceId, $clientToken, $dataSource, $ignoreClientToken);

        // 4. Add count from $value (for validation of direct uploads)
        if (is_array($value)) {
            $count += count($value);
        } elseif (filled($value) && ! is_numeric($value)) {
            // Non-numeric filled value usually represents a single file upload in Laravel
            $count += 1;
        }

        return $count;
    }

    public function countMediaInCollections(
        ResolvedModel $resolvedModel,
        array $collections,
        ?string $instanceId = null,
        ?string $clientToken = null,
        ?string $dataSource = null,
    ): int {
        return $this->getEffectiveMediaCount(
            $collections,
            $resolvedModel,
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
    public function temporaryUploadsHaveAnyMedia(array $collections, ?string $instanceId, ?string $clientToken, ?string $dataSource): bool
    {
        return $this->countTemporaryUploadsInCollections($collections, $instanceId, $clientToken, $dataSource) > 0;
    }
}
