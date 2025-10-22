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
        public Media|TemporaryUpload|null $singleMedium = null, // when provided, skip collection lookups and use this medium
        public array $collections = [],
        public array $options = [],
        public bool $disabled = false,
        public bool $readonly = false,
        public bool $selectable = false,
    ) {
        // TODO id mmp?
        parent::__construct($id);

        $this->resolveModelOrClassName($modelOrClassName);

        $this->media = collect();
        Log::info($this->singleMedium?->id . ' <-- this->singleMedium id in mmp');
//        Log::info($this->modelOrClassName . ' <-- this modelOrClassName in mmp');

        // CASE 1: If a single medium is provided, use only that.
        if ($this->singleMedium instanceof Media || $this->singleMedium instanceof TemporaryUpload) {
            Log::info($this->singleMedium?->id . ' id of $this->singleMedium id in mmp of if branch');
            $this->media->push($this->singleMedium);
        } else {
            $this->media = collect($collections)
                ->filter(fn($collectionName
                ) => !is_null($collectionName) && $collectionName !== '') // remove null or empty
                ->flatMap(function (?string $collectionName, string $collectionType) {
                    if ($this->temporaryUploadMode) {
                        if (!empty($collectionName)) {
                            return TemporaryUpload::forCurrentSession($collectionName);
                        }
                    }

                    if ($this->model) {
                        return $this->model->getMedia($collectionName);
                    }

                    return [];
                })
                ->sortBy(fn($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
                ->values();
        }

        // merge into config
        $this->initializeConfig();

        // TODO is there a neater way to do this?
        // options are passed to components, config is reinitialized for each component.
        // override hide media menu when nothing to see inside menu
        // since i use config have to do this after config has been initialized
        if (
            $this->getConfig('showDestroyButton') === false &&
            $this->getConfig('showSetAsFirstButton') === false &&
            $this->getConfig('showMediaEditButton') === false

        ) {
            $this->options['showMenu']  = false;
            $this->config['showMenu']  = false;
        }
    }

    public function render(): View
    {
        return $this->getView('media-manager-preview', $this->getConfig('frontendTheme'));
    }
}
