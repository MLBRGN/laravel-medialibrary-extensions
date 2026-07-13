<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Video extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public function __construct(
        public Media|TemporaryUpload $medium,
        public bool $previewMode = true,
        array $options = [],
        ?string $id = null,
    ) {
        parent::__construct($id);

        $this->options = $options;

        $this->resolveConfig();
    }

    protected function domIdSuffix(): string
    {
        return 'video';
    }

    public function render(): View
    {
        return $this->renderView('', null, false, 'medialibrary-extensions::components.video');
    }
}
