<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManagerPreview extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public Collection $media;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload|null $medium = null, // when provided, skip collection lookups and just use this medium
        public array $collections = [], // in image, document, youtube, video, audio
        public array $options = [],
        public bool $disabled = false,
        public bool $readonly = false,
        public bool $selectable = false,
    ) {
//        Log::info('options: ' . print_r($options, true));
        $frontendTheme = $this->options['frontendTheme'] ?? config('media-library-extensions.frontend_theme', 'bootstrap-5');
        parent::__construct($id, $frontendTheme);

        $this->resolveModelOrClassName($modelOrClassName);

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
                if ($this->temporaryUploadMode) {
                    return TemporaryUpload::forCurrentSession($collectionName);
                }

                if ($this->model) {
                    return $this->model->getMedia($collectionName);
                }

                return [];
            })
            ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
            ->values();

        // merge into config
        $this->initializeConfig([
            'frontendTheme' => $this->frontendTheme,
            'useXhr' => $this->options['useXhr'] ?? config('media-library-extensions.use_xhr', true),
        ]);
    }

    public function render(): View
    {
        return $this->getView('media-manager-preview', $this->frontendTheme);
    }
}
