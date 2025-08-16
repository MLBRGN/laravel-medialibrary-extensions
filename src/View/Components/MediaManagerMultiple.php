<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Facades\Blade;
use Spatie\MediaLibrary\HasMedia;

class MediaManagerMultiple extends MediaManager
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
        string $allowedMimeTypes = '',
    ) {
        parent::__construct(
            modelOrClassName: $modelOrClassName,
            imageCollection: $imageCollection,
            documentCollection: $documentCollection,
            youtubeCollection: $youtubeCollection,
            videoCollection: $videoCollection,
            audioCollection: $audioCollection,
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
            allowedMimeTypes: $allowedMimeTypes,
        );
    }
}
