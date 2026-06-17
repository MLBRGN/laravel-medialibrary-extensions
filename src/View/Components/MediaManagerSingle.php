<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerSingle extends MediaManager
{
    public int $totalMediaCount = 0;

    public function __construct(
        ?string $id,
        mixed $modelOrClassName,
        public Media|TemporaryUpload|null $singleMedia = null, // when provided, skip collection lookups and use this medium
        array $collections = [],
        array $options = [],
        bool $disabled = false,
        bool $readonly = false,
        bool $selectable = false,
    ) {
        // override options
        $options['showOrder'] = false; // should always be false

        parent::__construct(
            id: $id,
            modelOrClassName: $modelOrClassName,
            singleMedia: $singleMedia,
            collections: $collections,
            options: $options,
            multiple: false,
            disabled: $disabled,
            readonly: $readonly,
            selectable: $selectable,
        );

        $this->options = $options;

        // when singleMedia provided, dont count collections
        if ($this->singleMedia !== null) {
            $this->totalMediaCount = 1;
        } else {
            foreach ($collections as $collectionName) {
                if ($modelOrClassName instanceof HasMedia) {
                    $this->totalMediaCount += $modelOrClassName->getMedia($collectionName)->count();
                } elseif (is_string($modelOrClassName)) {
                    $this->totalMediaCount += TemporaryUpload::forCurrentClient($collectionName, $this->instanceId)->count();
                }
            }
        }

        // TODO implement disabled and readonly, this is not per se the same as disableForm

        // boolean property to disable form(s) in blade view(s)
        $this->setOption('disableForm', $this->totalMediaCount >= 1);

        $this->resolveConfig();

    }
}
