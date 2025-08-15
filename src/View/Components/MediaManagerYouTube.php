<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Spatie\MediaLibrary\HasMedia;

class MediaManagerYouTube extends MediaManager
{
    public function __construct(
        HasMedia|string $modelOrClassName,
        string $youtubeCollection = '',
        bool $uploadEnabled = false,
        string $uploadFieldName = 'media',
        bool $destroyEnabled = false,
        bool $setAsFirstEnabled = false,
        bool $showMediaUrl = false,
        bool $showOrder = false,
        bool $showMenu = true,
        string $id = '',
        ?string $frontendTheme = null,
        ?bool $useXhr = true,
        ?bool $multiple = true,
    ) {
        parent::__construct(
            modelOrClassName: $modelOrClassName,
            imageCollection: '',// always empty
            documentCollection: '',// always empty
            youtubeCollection: $youtubeCollection,
            videoCollection: '',// always empty
            audioCollection: '',// always empty
            uploadEnabled: $uploadEnabled,
            uploadFieldName: $uploadFieldName,
            destroyEnabled: $destroyEnabled,
            setAsFirstEnabled: $setAsFirstEnabled,
            showMediaUrl: $showMediaUrl,
            showOrder: $showOrder,
            showMenu: $showMenu,
            id: $id,
            frontendTheme: $frontendTheme,
            useXhr: $useXhr,
            multiple: $multiple,
        );
    }
}
