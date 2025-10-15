<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptions;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerPreview extends BaseComponent
{
    use InteractsWithOptions;
    use ResolveModelOrClassName;

    public ?bool $useXhr = true;

    public ?string $frontendTheme = null;

    public bool $disabled = false;

    public bool $readonly = false;

    public bool $selectable = false;

    public bool $showDestroyButton = false;

    public bool $showMediaEditButton = false; // (at the moment) only for image editing

    public bool $showMenu = true;

    public bool $showOrder = false;

    public bool $showSetAsFirstButton = false;

    public bool $temporaryUploads = false;

    public string $allowedMimeTypes = '';

    public Collection $media;

    protected array $optionKeys = [
        'allowedMimeTypes',
        'disabled',
        'readonly',
        'selectable',
        'showDestroyButton',
        'showMediaEditButton',
        'showMenu',
        'showOrder',
        'showSetAsFirstButton',
        'temporaryUploads',
        'useXhr',
        //        'frontendTheme',
    ];

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload|null $medium = null, // when provided, skip collection lookups and just use this medium
        public array $collections = [], // in image, document, youtube, video, audio
        public array $options = [],
    ) {
        //        dd($options);
        //        dd('temporaryUploads: ' . $this->temporaryUploads ? 'Yes' : 'No');
        $frontendTheme = $this->options['frontendTheme'] ?? config('media-library-extensions.frontend_theme', 'bootstrap-5');
        parent::__construct($id, $frontendTheme);

        $this->resolveModelOrClassName($modelOrClassName);

        // apply matching options to class properties
        $this->mapOptionsToProperties($this->options);
        //        dd($this->temporaryUploads);

        // when non of the menu items visible, set showMenu to false
        // TODO
        //        if (!$showDestroyButton && !$showOrder && !$showSetAsFirstButton && !$showMediaEditButton) {
        //            $this->showMenu = false;
        //        }

        $this->media = collect();

        // CASE 1: If a single medium is provided, use only that.
        if ($this->medium instanceof Media) {
            $this->media->push($this->medium);

            return;
        }

        // CASE 2: Otherwise collect from configured collections.
        $this->media = collect($collections)
            ->flatMap(function (string $collectionName) {
                if ($this->temporaryUploads) {
                    return TemporaryUpload::forCurrentSession($collectionName);
                }

                if ($this->model) {
                    return $this->model->getMedia($collectionName);
                }

                return [];
            })
            ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
            ->values();
        //        dd($this->media);
    }

    public function render(): View
    {
        return $this->getView('media-manager-preview', $this->frontendTheme);
    }
}
