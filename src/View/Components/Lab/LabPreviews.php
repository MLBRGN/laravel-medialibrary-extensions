<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Lab;

/*
 * Edit media and restore original if needed
 */

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LabPreviews extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public function __construct(
        ?string $id,
        public Media|TemporaryUpload|null $media,
        array $options = [],
        public ?string $dataSource = 'default'
    ) {
        parent::__construct($id);
        $this->options = $options;

        $this->resolveConfig();
    }

    protected function domIdSuffix(): string
    {
        return 'lab-previews';
    }

    public function render(): View
    {
        return $this->renderView('lab.lab-previews', $this->getConfig('theme'));
    }
}
