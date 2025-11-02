<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Lab;

/*
 * Edit media and restore original if needed
 */

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LabPreviewOriginal extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public ?array $imageInfo = null;

    public function __construct(
        ?string $id,
        public Media|TemporaryUpload|null $medium,
        public array $options = []
    ) {
        parent::__construct($id);

        $this->initializeConfig();

        $this->imageInfo = $medium->model?->getOriginalImageInfo($medium);
    }

    public function render()
    {
        return $this->getView('lab.lab-preview-original', $this->getConfig('frontendTheme'));

    }
}
