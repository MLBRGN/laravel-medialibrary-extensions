<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerSingle extends MediaManager
{
    public function __construct(
        mixed $modelOrClassName,
        ?Media $medium = null,// when provided, skip collection lookups and just use this medium
        string $imageCollection = '',
        string $documentCollection = '',
        string $youtubeCollection = '',
        string $videoCollection = '',
        string $audioCollection = '',
        bool $showUploadForm = true,
        string $uploadFieldName = 'media',
        bool $showDestroyButton = false,
        bool $showOrder = false,
        bool $showMenu = true,
        string $id = '',
        ?string $frontendTheme = null,
        ?bool $useXhr = true,
        string $allowedMimeTypes = '',
        public bool $showMediaEditButton = false,// (at the moment) only for image editing
        public bool $readonly = false,
        public bool $disabled = false,
    ) {
        parent::__construct(
            modelOrClassName: $modelOrClassName,
            medium: $medium,
            imageCollection: $imageCollection,
            documentCollection: $documentCollection,
            youtubeCollection: $youtubeCollection,
            videoCollection: $videoCollection,
            audioCollection: $audioCollection,
            showUploadForm: $showUploadForm,
            uploadFieldName: $uploadFieldName,
            showDestroyButton: $showDestroyButton,
            showSetAsFirstButton: false,// always false
            showOrder: $showOrder,
            showMenu: $showMenu,
            id: $id,
            frontendTheme: $frontendTheme,
            useXhr: $useXhr,
            multiple: false,// always false
            allowedMimeTypes: $allowedMimeTypes,
            selectable: false,// always false
            showMediaEditButton: $showMediaEditButton,
            readonly: $readonly,
            disabled: $disabled,
        );

        $collections = [
            $imageCollection,
            $documentCollection,
            $youtubeCollection,
            $videoCollection,
            $audioCollection,
        ];

        $totalMediaCount = 0;

        foreach ($collections as $collectionName) {
            if ($modelOrClassName instanceof HasMedia) {
                $totalMediaCount += $modelOrClassName->getMedia($collectionName)->count();
            } elseif (is_string($modelOrClassName)) {
                $totalMediaCount += TemporaryUpload::forCurrentSession($collectionName)->count();
            }
        }

        // TODO implement disabled and readonly, this is not perse the same as disableForm
        // boolean property to disable form(s) in blade view(s)
        $this->disableForm = $totalMediaCount >= 1;
    }
}
