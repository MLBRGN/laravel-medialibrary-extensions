<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Mlbrgn\MediaLibraryExtensions\Services\MediaService;

class MediaManagerMultiple extends MediaManager
{
    public function __construct(
        ?string $id,
        mixed $modelOrClassName,
        array $collections = [],
        array $options = [],
        bool $multiple = true,
        bool $disabled = false,
        bool $readonly = false,
        bool $selectable = false,
        public ?string $dataSource = 'default',
    ) {
//        dump('dataSource '.$dataSource);
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
            dataSource: $dataSource,
        );
        $this->options = $options;


//        $resolved = $this->mediaService->resolveModelOrClassName($modelOrClassName, $dataSource);

//        $this->totalMediaCount = $this->mediaService->countMediaInCollections(
//            $resolved,
//            $collections,
//            $this->instanceId,
//            $this->clientToken,
//            $dataSource
//        );

        $this->maxMediaCount = config('medialibrary-extensions.max_items_in_shared_media_collections');

    }

    protected function domIdSuffix(): string {
        return 'mmm';
    }
}
