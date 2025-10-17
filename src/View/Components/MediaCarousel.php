<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class MediaCarousel extends BaseComponent
{
    use ResolveModelOrClassName;
    use InteractsWithOptionsAndConfig;

    public MediaCollection $mediaItems;

    public MediaCollection $media; // TODO duplocate with $mediaItems

    public int $mediaCount;

    public string $previewerId = '';

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,
        public ?string $mediaCollection = null,// TODO why do i have 2
        public ?array $mediaCollections = [],
        public bool $singleMedium = false,
        public bool $expandableInModal = true,
        public array $options = [],
        public bool $inModal = false,
        public ?string $frontendTheme = null,// TODO move to options

    ) {
        parent::__construct($id, $frontendTheme);

        $this->resolveModelOrClassName($modelOrClassName);

        // Merge media from multiple or single collection
        $allMedia = collect(
            $this->mediaCollections ?: [$this->mediaCollection]
        )
            ->filter()// remove false values
            ->reduce(function (Collection $carry, string $collectionName) {
                if ($this->temporaryUploadMode) {
                    return $carry->merge(TemporaryUpload::forCurrentSession($collectionName));
                }

                return $carry->merge($this->model->getMedia($collectionName));
            }, collect());

        // Sort by 'priority' custom property (both TemporaryUpload and Media support getCustomProperty)
        $allMedia = $allMedia
            ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
            ->values();

        $this->mediaItems = MediaCollection::make($allMedia);
        $this->media = $this->mediaItems;

        $this->mediaCount = $this->mediaItems->count();
        $this->frontendTheme = $frontendTheme ? $this->frontendTheme : config('media-library-extensions.frontend_theme', 'plain');
        //        $this->id = $this->id.'-carousel';
        $this->id = $this->id.'-crs';

        // merge into config
        $this->initializeConfig([
            'frontendTheme' => $this->frontendTheme,
            'useXhr' => $this->options['useXhr'] ?? config('media-library-extensions.use_xhr', true),
//            'csrfToken' => csrf_token(),
        ]);
    }

    public function render(): View
    {
        return $this->getView('media-carousel', $this->frontendTheme);
    }
}
