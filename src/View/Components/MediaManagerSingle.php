<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerSingle extends MediaManager
{
    public function __construct(
        ?string $id,
        // New preferred API: modelReference (camelCase => model-reference in Blade)
        mixed $modelReference = null,
        // Backward compatibility: modelOrClassName still accepted
        mixed $modelOrClassName = null,
        public Media|TemporaryUpload|null $singleMedia = null, // when provided, skip collection lookups and use this medium
        array $collections = [],
        array $options = [],
        bool $disabled = false,
        bool $readonly = false,
        bool $selectable = false,
        public ?string $dataSource = 'default',
    ) {
        // override options
        $options['showOrder'] = false; // should always be false

        parent::__construct(
            id: $id,
            modelReference: $modelReference ?? $modelOrClassName,
            modelOrClassName: $modelOrClassName,
            singleMedia: $singleMedia,
            collections: $collections,
            options: $options,
            multiple: false,
            disabled: $disabled,
            readonly: $readonly,
            selectable: $selectable,
            dataSource: $dataSource,
        );
        // For the dedicated Single component, keep the "Set as first" button available in config.
        // Blades already ensure it is disabled visually for singles.
        $this->setOption('showSetAsFirstButton', true);
        $this->resolveConfig();
    }
}
