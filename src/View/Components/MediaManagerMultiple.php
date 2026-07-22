<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class MediaManagerMultiple extends MediaManager
{
    public function __construct(
        ?string $id,
        // Preferred new prop (camelCase => model-reference in Blade)
        mixed $modelReference = null,
        // Backward-compatible legacy prop
        mixed $modelOrClassName = null,
        array $collections = [],
        array $options = [],
        bool $multiple = true,
        bool $disabled = false,
        bool $readonly = false,
        bool $selectable = false,
        public ?string $dataSource = 'default',
    ) {
        // Normalize: prefer new prop; keep both in sync
        if ($modelReference !== null) {
            $modelOrClassName = $modelReference;
        } elseif ($modelOrClassName !== null) {
            $modelReference = $modelOrClassName;
        }

        parent::__construct(
            id: $id,
            modelReference: $modelReference,
            modelOrClassName: $modelOrClassName,
            singleMedia: null,// always null
            collections: $collections,
            options: $options,
            multiple: $multiple,
            disabled: $disabled,
            readonly: $readonly,
            selectable: $selectable,
            dataSource: $dataSource,
        );
        $this->options = $options;

        $this->maxMediaCount = config('medialibrary-extensions.max_items_in_shared_media_collections');

    }
}
