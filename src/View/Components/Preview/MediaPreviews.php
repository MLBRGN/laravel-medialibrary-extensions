<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Preview;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPreviews extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public Collection $media;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public array $collections = [],
        public array $options = [],
        public Media|TemporaryUpload|null $singleMedium = null, // when provided, skip collection lookups and use this medium
        public bool $multiple = false,
        public bool $disabled = false,
        public bool $readonly = false,
        public bool $selectable = false,
    ) {
        parent::__construct($id);

        $this->resolveModelOrClassName($modelOrClassName);
//        $this->initializeConfig();

        $this->media = collect();
//        Log::info($this->singleMedium?->id . ' <-- this->singleMedium id in mmp');
//        Log::info($this->modelOrClassName . ' <-- this modelOrClassName in mmp');

//        Log::info([
//            'singleMedium' => $this->singleMedium?->id,
//            'isMedia' => $this->singleMedium instanceof Media,
//            'isTemporaryUpload' => $this->singleMedium instanceof TemporaryUpload,
//        ]);

//        if (isset($singleMedium)) {
//            Log::info('singleMedium set '. $singleMedium);
//        }
        // CASE 1: If a single medium is provided, use only that.
        if ($this->singleMedium instanceof Media || $this->singleMedium instanceof TemporaryUpload) {
//            Log::info('singleMedium detected');
//            Log::info($this->singleMedium?->id . ' id of $this->singleMedium id in mmp of if branch');
            $this->media->push($this->singleMedium);
        } else {
//            Log::info('no singleMedium detected');
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

//        Log::info('Media IDs: ' . implode(', ', $this->media->pluck('id')->all()));

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
        return $this->getView('preview.media-previews', $this->getConfig('frontendTheme'));
    }
}
