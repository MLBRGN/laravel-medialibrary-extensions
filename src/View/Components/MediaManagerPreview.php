<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerPreview extends BaseComponent
{
    use ResolveModelOrClassName;

    public string $allowedMimeTypes = '';

    public Collection $media;

    public function __construct(
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public string $id = '',
        public ?string $imageCollection = '',
        public ?string $documentCollection = '',
        public ?string $youtubeCollection = '',
        public ?string $videoCollection = '',
        public ?string $audioCollection = '',
        public ?string $frontendTheme = null,
        public bool $destroyEnabled = false,
        public bool $setAsFirstEnabled = false,
        public bool $showOrder = false,
        public bool $showMenu = true,
        public bool $temporaryUploads = false,
        public ?bool $useXhr = true,
        public bool $selectable = false,
    ) {
        parent::__construct($id, $frontendTheme);

        $this->resolveModelOrClassName($modelOrClassName);

        // when non of the menu items visible, set showMenu to false
        if (!$destroyEnabled && !$showOrder && !$setAsFirstEnabled) {
            $this->showMenu = false;
        }

        $collectionNames = collect([
            $imageCollection,
            $youtubeCollection,
            $documentCollection,
            $videoCollection,
            $audioCollection,
        ])->filter(); // remove falsy values

        $this->media = $collectionNames
            ->reduce(function ($carry, $collectionName) use ($temporaryUploads) {
                if ($temporaryUploads) {
                    return $carry->merge(TemporaryUpload::forCurrentSession($collectionName));
                }

                if ($this->model) {
                    return $carry->merge($this->model->getMedia($collectionName));
                }

                return $carry;
            }, collect())
            ->sortBy(fn($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
            ->values();

    }

    public function render(): View
    {
        return $this->getView('media-manager-preview', $this->frontendTheme);
    }
}
