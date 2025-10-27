<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

class MediaManagerMultiple extends MediaManager
{
    public function __construct(
        ?string $id,
        mixed $modelOrClassName,
        array $collections = [],
        array $options = [],
        bool $multiple = false,
        bool $disabled = false,
        bool $readonly = false,
        bool $selectable = false,
    ) {
        parent::__construct(
            id: $id,
            modelOrClassName: $modelOrClassName,
            singleMedium: null,// always null
            collections: $collections,
            options: $options,
            multiple: $multiple,
            disabled: $disabled,
            readonly: $readonly,
            selectable: $selectable,
        );

    }
}
