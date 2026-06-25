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

class LabPreviewOriginal extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public ?array $imageInfo = null;

    public function __construct(
        ?string $id,
        public Media|TemporaryUpload|null $media,
        array $options = []
    ) {
        parent::__construct($id);
        $this->options = $options;

        $this->resolveConfig();

        $this->imageInfo = $media->model?->getOriginalImageInfo($media);
    }

    protected function domIdSuffix(): string {
        return 'lab-preview-original';
    }

    public function render(): View
    {
        return $this->renderView('lab.lab-preview-original', $this->getConfig('frontendTheme'));
    }
}
