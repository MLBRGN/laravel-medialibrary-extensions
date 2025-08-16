<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;

class MediaManagerSingle extends MediaManager
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
            setAsFirstEnabled: false,// always false
            showMediaUrl: $showMediaUrl,
            showOrder: $showOrder,
            showMenu: $showMenu,
            id: $id,
            frontendTheme: $frontendTheme,
            useXhr: $useXhr,
            multiple: false,// always false
            allowedMimeTypes: $allowedMimeTypes,
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

        // boolean property to disable form(s) in blade view(s)
        $this->disableForm = $totalMediaCount >= 1;
    }
}
