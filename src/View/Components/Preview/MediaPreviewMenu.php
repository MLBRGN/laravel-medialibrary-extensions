<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Preview;

use Illuminate\Contracts\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPreviewMenu extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public $medium,
        public array $collections = [],
        public array $options = [],
        public Media|TemporaryUpload|null $singleMedium = null, // when provided, skip collection lookups and use this medium
        public bool $disabled = false,
        public bool $selectable = false,
        public bool $readonly = false,
        public bool $multiple = false,
    ) {
        parent::__construct($id);
        $this->initializeConfig();

    }

    public function render(): View
    {
        return $this->getView('preview.media-preview-menu', $this->getConfig('frontendTheme'));
    }
}
