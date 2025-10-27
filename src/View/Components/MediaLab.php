<?php
/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

/*
 * Edit media and restore original if needed
 */

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLab extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public string $mediaManagerLabPreviewUpdateRoute = '';

    public function __construct(
        ?string $id,
        public Media|TemporaryUpload|null $medium,
    ) {
        $id = filled($id) ? $id : 'mle-media-lab';

        parent::__construct($id);

        $this->mediaManagerLabPreviewUpdateRoute = route(mle_prefix_route('media-manager-lab-preview-update'));

        $this->initializeConfig();
    }

    public function render(): View
    {
        return $this->getView('media-lab', $this->getConfig('frontendTheme'));

    }
}
