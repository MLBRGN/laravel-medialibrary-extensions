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
    ) {
        parent::__construct(
            id: $id,
            modelOrClassName: $modelOrClassName,
            singleMedium: null,// always null
            collections: $collections,
            options: $options,
            multiple: true,
        );

    }
}
