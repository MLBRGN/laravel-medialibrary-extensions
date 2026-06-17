<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Spatie\MediaLibrary\HasMedia;

class MediaManagerMultiple extends MediaManager
{
    public int $totalMediaCount = 0;

    public function __construct(
        ?string $id,
        mixed $modelOrClassName,
        array $collections = [],
        array $options = [],
        bool $multiple = true,
        bool $disabled = false,
        bool $readonly = false,
        bool $selectable = false,
    ) {
        parent::__construct(
            id: $id,
            modelOrClassName: $modelOrClassName,
            singleMedia: null,// always null
            collections: $collections,
            options: $options,
            multiple: $multiple,
            disabled: $disabled,
            readonly: $readonly,
            selectable: $selectable,
        );
        $this->options = $options;

        $mediaService = app(MediaService::class);
        $dataSource = $this->options['dataSource'] ?? null;

        $resolved = $mediaService->resolveModelOrClassName($modelOrClassName, $dataSource);

        if ($modelOrClassName instanceof HasMedia) {
            $this->totalMediaCount = $mediaService->countModelMediaInCollections($resolved->model, $collections, $dataSource);
        } elseif (is_string($modelOrClassName)) {
            $instanceId = $this->options['instanceId'] ?? null;
            $clientToken = $this->options['clientToken'] ?? null;
            $this->totalMediaCount = $mediaService->countTemporaryUploadsInCollections($collections, $instanceId, $clientToken, $dataSource);
        }

    }
}
