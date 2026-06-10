<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Audio extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public string $id;

    public function __construct(
        public Media|TemporaryUpload $medium,
        public bool $previewMode = true,
        array $options = [],
    ) {
        parent::__construct();
        $this->options = $options;
        $this->setBaseId('mle-audio-'.$medium->id);

        $this->resolveConfig();
    }

    public function render(): View
    {
        return $this->renderView('', null, false, 'medialibrary-extensions::components.audio');
    }
}
