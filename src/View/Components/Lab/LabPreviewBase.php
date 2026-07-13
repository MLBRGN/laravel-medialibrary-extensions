<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Lab;

/*
 * Edit media and restore original if needed
 */

use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LabPreviewBase extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public $requiredAspectRatio;

    public ?array $imageInfo = null;

    public function __construct(
        ?string $id,
        public Media|TemporaryUpload|null $media,
        array $options = [],
        public ?string $dataSource = 'default'
    ) {
        parent::__construct($id);
        $this->options = $options;

        $this->resolveConfig();

        if ($this->media instanceof Media) {
            $parentModel = $this->media->model;

            if (method_exists($parentModel, 'getRequiredMediaAspectRatio')) {
                $this->requiredAspectRatio = $parentModel->getRequiredMediaAspectRatio($this->media);
            }

            if (method_exists($parentModel, 'getImageInfo')) {
                $this->imageInfo = $media->model->getBaseImageInfo($media, $this->requiredAspectRatio);
            }
        }
    }

    protected function domIdSuffix(): string
    {
        return 'base';
    }

    public function render(): View
    {
        return $this->renderView('lab.lab-preview-base', $this->getConfig('theme'));
    }
}
