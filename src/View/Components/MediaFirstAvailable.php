<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// TODO dataSource?
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
        array $options = [],
        public bool $previewMode = false // should the media-viewer be in preview mode (no autoplay, no document loading or not)
    ) {
        parent::__construct($id ?: null);
        $this->options = $options;
        $this->resolveModelOrClassName($modelOrClassName, 'default');

        if (! $this->hasCollections()) {
            throw new Exception(__('medialibrary-extensions::messages.no_media_collections'));
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

        $this->resolveConfig();
    }

    public function render(): View
    {
        return $this->renderView('', null, false, 'medialibrary-extensions::components.media-first-available');
    }
}
