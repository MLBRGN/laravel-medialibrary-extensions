<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

class MediaManagerMultiple extends MediaManager
{
    public function __construct(
        ?string $id,
        mixed $modelReference,
        array $collections = [],
        array $options = [],
        bool $multiple = true,
        bool $disabled = false,
        bool $readonly = false,
        bool $selectable = false,
        public ?string $dataSource = 'default',
    ) {
        parent::__construct(
            id: $id,
            modelReference: $modelReference,
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
