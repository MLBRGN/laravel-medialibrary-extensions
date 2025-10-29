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

class LabPreviewBase extends BaseComponent
{
    use InteractsWithOptionsAndConfig;


    public $requiredAspectRatio;
    public $imageInfo;

    public function __construct(
        ?string $id,
        public Media|TemporaryUpload|null $medium,
    ) {
        parent::__construct($id);

        $this->initializeConfig();

        if ($this->medium instanceof Media) {
            $parentModel = $this->medium->model;

            if (method_exists($parentModel, 'getMediaConversionsWithAspectRatio')) {
                $this->requiredAspectRatio = $parentModel->getMediaConversionsWithAspectRatio($this->medium);
            }

            if (method_exists($parentModel, 'getImageInfo')) {
                $this->imageInfo = $medium->model->getImageInfo($medium);
            }
        }
    }

    public function render()
    {
        return $this->getView('lab.lab-preview-base', $this->getConfig('frontendTheme'));

    }
}
