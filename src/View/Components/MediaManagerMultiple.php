<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Spatie\MediaLibrary\HasMedia;

class MediaManagerMultiple extends MediaManager
{
    public function __construct(
        HasMedia|string|null $modelOrClassName = null,
        string $imageCollection = '',
        string $documentCollection = '',
        string $youtubeCollection = '',
        bool $uploadEnabled = false,
        string $uploadFieldName = 'media',
        bool $destroyEnabled = false,
        bool $setAsFirstEnabled = false,
        bool $showMediaUrl = false,
        bool $showOrder = false,
        string $id = '',
        ?string $frontendTheme = null,
        ?bool $useXhr = true,
    )
    {
        parent::__construct(
            modelOrClassName: $modelOrClassName,
            imageCollection: $imageCollection,
            documentCollection: $documentCollection,
            youtubeCollection: $youtubeCollection,
            uploadEnabled: $uploadEnabled,
            uploadFieldName: $uploadFieldName,
            destroyEnabled: $destroyEnabled,
            setAsFirstEnabled: $setAsFirstEnabled,
            showMediaUrl: $showMediaUrl,
            showOrder: $showOrder,
            id: $id,
            frontendTheme: $frontendTheme,
            useXhr: $useXhr,
            multiple: true,
        );
    }
}
