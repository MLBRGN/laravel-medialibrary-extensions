<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Preview;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPreviewGrid extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public array $collections = [],
        array $options = [],
        public Media|TemporaryUpload|null $singleMedia = null, // when provided, skip collection lookups and use this medium
        public bool $disabled = false,
        public bool $selectable = false,
        public bool $readonly = false,
        public bool $multiple = false,
        public string $instanceId = '',
        public ?string $dataSource = null,
    ) {
        parent::__construct($id);
        if ($instanceId) {
            $this->instanceId = $instanceId;
        }
        $this->options = $options;

        $this->resolveConfig();
    }

    public function render(): View
    {
        return $this->renderView('preview.media-preview-grid', $this->getConfig('frontendTheme'));
    }
}
