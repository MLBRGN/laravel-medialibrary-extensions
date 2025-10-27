<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Preview;

use Illuminate\Contracts\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class MediaPreviewItemEmpty extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public function __construct(
        ?string $id,
        public array $options = [],
    ) {

        parent::__construct($id);

        $this->initializeConfig();

    }
    public function render(): View
    {
        return $this->getView('preview.media-preview-item-empty', $this->getConfig('frontendTheme'));
    }
}
