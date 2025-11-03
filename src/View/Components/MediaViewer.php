<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaViewer extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public ?string $mediumType;
    public ?string $componentToRender;

    public function __construct(
        ?string $id,
        public Media|TemporaryUpload|null $medium,
        public array $options = [],
        public bool $previewMode = true, // should the media-viewer be in preview mode (no autoplay, no document loading or not)
        public bool $expandableInModal = false // can this medium be opened in a modal when clicking it
    )
    {
        parent::__construct($id);

        $this->mediumType = getMediaType($this->medium);
        $this->componentToRender = $this->resolveComponentForMedium($this->medium);
    }

    public function render(): View
    {
        return $this->getView('media-viewer', $this->getConfig('frontendTheme'));
    }
}
