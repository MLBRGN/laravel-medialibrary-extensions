<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaFirstAvailable extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public ?Media $medium = null;

    public ?string $componentToRender;

    public ?string $mediumType;
    public bool $expandableInModal = false;

    public function __construct(
        public string $id,
        public mixed $modelOrClassName,
        public ?array $collections = [],
        public array $options = [],
        public bool $previewMode = false // should the media-viewer be in preview mode (no autoplay, no document loading or not)
    ) {
        parent::__construct($id ?: null);

        $this->resolveModelOrClassName($modelOrClassName);

        if (! $this->hasCollections()) {
            throw new Exception(__('media-library-extensions::messages.no_media_collections'));
        }

        if ($this->temporaryUploadMode) {
            throw new Exception('Temporary uploads not implemented');
        }

        // pick the first available medium
        $this->medium = collect($this->collections ?? [])
            ->map(fn (string $collection) => $this->model->getFirstMedia($collection))
            ->filter()
            ->first();

        $this->mediumType = getMediaType($this->medium);
        $this->componentToRender = $this->resolveComponentForMedium($this->medium);

        $this->initializeConfig();
    }

    public function render(): View
    {
        return view('media-library-extensions::components.media-first-available');
    }
}
