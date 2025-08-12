<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Spatie\MediaLibrary\HasMedia;

class MediaManagerYouTube extends MediaManager
{
    public function __construct(
        HasMedia|string $modelOrClassName,
        string $imageCollection = '',
        string $documentCollection = '',
        string $youtubeCollection = '',
        string $videoCollection = '',
        string $audioCollection = '',
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
    ) {
        parent::__construct(
            modelOrClassName: $modelOrClassName,
            imageCollection: '',
            documentCollection: '',
            youtubeCollection: $youtubeCollection,
            videoCollection: '',
            audioCollection: '',
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
            multiple: true,
        );
    }
}
