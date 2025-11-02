<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Preview;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPreviewGrid extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    //    public Collection $media;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public array $collections = [],
        public array $options = [],
        public Media|TemporaryUpload|null $singleMedium = null, // when provided, skip collection lookups and use this medium
        public bool $disabled = false,
        public bool $selectable = false,
        public bool $readonly = false,
        public bool $multiple = false,
    ) {
        parent::__construct($id);
    }

    public function render(): View
    {
        return $this->getView('preview.media-preview-grid', $this->getConfig('frontendTheme'));
    }
}
