<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Preview;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class MediaPreviewItemEmpty extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public function __construct(
        ?string $id,
        array $options = [],
        public ?string $dataSource = 'default',
    ) {

        parent::__construct($id);
        $this->options = $options;

        $this->resolveConfig();
    }

    protected function domIdSuffix(): string
    {
        return 'media-preview-item-empty';
    }

    public function render(): View
    {
        return $this->getView('preview.media-preview-item-empty', $this->getConfig('theme'));
    }
}
