<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Facades\Blade;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\HasMedia;

class MediaManagerMultiple extends MediaManager
{
    public function __construct(
        mixed $modelOrClassName,
        string $imageCollection = '',
        string $documentCollection = '',
        string $youtubeCollection = '',
        string $videoCollection = '',
        string $audioCollection = '',
        bool $showUploadForm = true,
        string $uploadFieldName = 'media',
        bool $showDestroyButton = false,
        bool $showSetAsFirstButton = false,
        bool $showOrder = false,
        bool $showMenu = true,
        string $id = '',
        ?string $frontendTheme = null,
        ?bool $useXhr = true,
        string $allowedMimeTypes = '',
        public bool $selectable = false,
        public bool $showMediaEditButton = false,// (at the moment) only for image editing
        public bool $readonly = false,
        public bool $disabled = false,
    ) {
        parent::__construct(
            modelOrClassName: $modelOrClassName,
            imageCollection: $imageCollection,
            documentCollection: $documentCollection,
            youtubeCollection: $youtubeCollection,
            videoCollection: $videoCollection,
            audioCollection: $audioCollection,
            showUploadForm: $showUploadForm,
            uploadFieldName: $uploadFieldName,
            showDestroyButton: $showDestroyButton,
            showSetAsFirstButton: $showSetAsFirstButton,
            showOrder: $showOrder,
            showMenu: $showMenu,
            id: $id,
            frontendTheme: $frontendTheme,
            useXhr: $useXhr,
            multiple: true,
            allowedMimeTypes: $allowedMimeTypes,
            selectable: $selectable,
            showMediaEditButton: $showMediaEditButton,
            readonly: $readonly,
            disabled: $disabled,
        );
    }
}
