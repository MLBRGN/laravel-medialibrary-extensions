<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerPreview extends BaseComponent
{
    use ResolveModelOrClassName;

    public string $allowedMimeTypes = '';

    public Collection $media;

    public function __construct(
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public ?Media $medium = null,// when provided, skip collection lookups and just use this medium
        public string $id = '',
        public ?string $imageCollection = '',
        public ?string $documentCollection = '',
        public ?string $youtubeCollection = '',
        public ?string $videoCollection = '',
        public ?string $audioCollection = '',
        public ?string $frontendTheme = null,
        public bool $showDestroyButton = false,
        public bool $showSetAsFirstButton = false,
        public bool $showOrder = false,
        public bool $showMenu = true,
        public bool $temporaryUploads = false,
        public ?bool $useXhr = true,
        public bool $selectable = false,
        public bool $showMediaEditButton = false,// (at the moment) only for image editing
        public bool $readonly = false,
        public bool $disabled = false,
    ) {
        parent::__construct($id, $frontendTheme);

        $this->resolveModelOrClassName($modelOrClassName);

        // when non of the menu items visible, set showMenu to false
        // TODO
//        if (!$showDestroyButton && !$showOrder && !$showSetAsFirstButton && !$showMediaEditButton) {
//            $this->showMenu = false;
//        }

        $this->media = collect();

        parent::__construct($id, $frontendTheme);

        $this->resolveModelOrClassName($modelOrClassName);

        $this->media = collect();

        // 🔹 CASE 1: If a single medium is provided, use only that.
        if ($this->medium instanceof Media) {
            $this->media->push($this->medium);
            return;
        }

        // 🔹 CASE 2: Otherwise collect from configured collections.
        $collectionNames = collect([
            $this->imageCollection,
            $this->youtubeCollection,
            $this->documentCollection,
            $this->videoCollection,
            $this->audioCollection,
        ])->filter();

        $this->media = $collectionNames
            ->flatMap(function (string $collectionName) {
                if ($this->temporaryUploads) {
                    return TemporaryUpload::forCurrentSession($collectionName);
                }

                if ($this->model) {
                    return $this->model->getMedia($collectionName);
                }

                return [];
            })
            ->sortBy(fn($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
            ->values();
//        $collectionNames = collect([
//            $imageCollection,
//            $youtubeCollection,
//            $documentCollection,
//            $videoCollection,
//            $audioCollection,
//        ])->filter(); // remove falsy values
//
//        $this->media = $collectionNames
//            ->reduce(function ($carry, $collectionName) use ($temporaryUploads) {
//                if ($temporaryUploads) {
//                    return $carry->merge(TemporaryUpload::forCurrentSession($collectionName));
//                }
//
//                if ($this->model) {
//                    return $carry->merge($this->model->getMedia($collectionName));
//                }
//
//                return $carry;
//            }, collect())
//            ->sortBy(fn($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
//            ->values();
//        $collectionNames = collect([
//            $imageCollection,
//            $youtubeCollection,
//            $documentCollection,
//            $videoCollection,
//            $audioCollection,
//        ])->filter(); // remove falsy values
//
//        $this->media = $collectionNames
//            ->reduce(function ($carry, $collectionName) use ($temporaryUploads) {
//                if ($temporaryUploads) {
//                    return $carry->merge(TemporaryUpload::forCurrentSession($collectionName));
//                }
//
//                if ($this->model) {
//                    return $carry->merge($this->model->getMedia($collectionName));
//                }
//
//                return $carry;
//            }, collect())
//            ->sortBy(fn($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
//            ->values();

    }

    public function render(): View
    {
        return $this->getView('media-manager-preview', $this->frontendTheme);
    }
}
