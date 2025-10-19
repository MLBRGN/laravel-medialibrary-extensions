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
        $collections = $this->mergeCollections($collections);

        parent::__construct(
            id: $id,
            modelOrClassName: $modelOrClassName,
            medium: null,// always null
            collections: $collections,
            options: $options,
            multiple: true,
        );

    }
}
