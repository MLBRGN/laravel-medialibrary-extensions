<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;

class MediaGallery extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    /**
     * The aggregated media items to display in the gallery.
     */
    public Collection $media;

    /**
     * Create a new component instance.
     *
     * @param  string  $id  Logical component identity.
     * @param  mixed  $modelReference  The model instance or class name.
     * @param  array  $collections  Media collections to include.
     * @param  string  $layout  Layout type: 'grid' or 'flex'.
     * @param  int  $columns  Number of columns for grid layout.
     * @param  string  $gap  CSS gap value (e.g., '1rem', '10px').
     * @param  bool  $expandable  Whether to enable modal expansion on click.
     * @param  string  $itemClass  Additional CSS classes for gallery items.
     * @param  array  $options  Additional options for configuration.
     * @param  string|null  $dataSource  The data source for the model.
     */
    public function __construct(
        string $id,
        public mixed $modelReference,
        public array $collections = [],
        public string $layout = 'grid',
        public int $columns = 3,
        public string $gap = '1rem',
        public bool $expandable = true,
        public string $itemClass = '',
        array $options = [],
        public ?string $dataSource = 'default',
    ) {
        parent::__construct($id, $modelReference, $dataSource);

        // Add gallery-specific properties to the unified configuration.
        $this->configKeys = array_merge($this->configKeys, [
            'layout',
            'columns',
            'gap',
            'expandable',
            'itemClass',
        ]);

        $this->options = $options;

        // Allow overriding temporary upload mode via options if needed.
        if (isset($options['temporaryUploadMode'])) {
            $this->temporaryUploadMode = (bool) $options['temporaryUploadMode'];
        }

        $this->media = $this->resolveMedia();
        $this->totalMediaCount = $this->media->count();

        $this->resolveConfig();
    }

    /**
     * Fetch and aggregate media from the specified collections, sorted by priority.
     */
    protected function resolveMedia(): Collection
    {
        return collect($this->collections)
            ->filter(fn ($collectionName) => ! is_null($collectionName) && $collectionName !== '')
            ->flatMap(function (?string $collectionName) {
                if ($this->temporaryUploadMode) {
                    return TemporaryUpload::getForCurrentClient(
                        $collectionName,
                        $this->instanceId,
                        $this->dataSource,
                        $this->clientToken
                    );
                }

                if ($this->model) {
                    return $this->model->getMedia($collectionName);
                }

                return [];
            })
            ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
            ->values();
    }

    /**
     * Define the DOM ID suffix for the gallery container.
     */
    protected function domIdSuffix(): string
    {
        return 'gal';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return $this->renderView('media-gallery', $this->getConfig('theme'));
    }
}
