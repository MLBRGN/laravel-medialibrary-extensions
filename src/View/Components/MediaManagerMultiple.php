<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

class MediaManagerMultiple extends MediaManager
{

    public function __construct(
        ?string $id,
        mixed $modelOrClassName,
        array $collections = [], // in image, document, youtube, video, audio
        array $options = [],
    ) {
        $collections = $this->mergeCollections($collections);
//        $options = $this->mergeOptions($options);
//        $options['multiple'] = true;

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
