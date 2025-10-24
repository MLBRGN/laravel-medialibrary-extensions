<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

/*
 * Edit media and restore original if needed
 */

use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLab extends BaseComponent
{
    use InteractsWithOptionsAndConfig;


    public function __construct(
        ?string $id,
        public Media|TemporaryUpload|null $medium,
    ) {
        parent::__construct($id);

        $this->initializeConfig();
    }

    public function render()
    {
        return $this->getView('media-lab', $this->getConfig('frontendTheme'));

    }
}
