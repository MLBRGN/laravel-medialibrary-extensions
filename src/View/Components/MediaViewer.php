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
        array $options = [],
        public bool $previewMode = true, // should the media-viewer be in preview mode (no autoplay, no document loading or not)
        public bool $expandableInModal = false, // can this medium be opened in a modal when clicking it
        public ?string $dataSource = 'default',
    ) {
        parent::__construct($id);
        $this->options = $options;

        $this->mediumType = getMediaType($this->medium);
        $this->componentToRender = $this->resolveComponentForMedium($this->medium);

        $this->resolveConfig();

        $this->addConfigDefaults([
            'previewMode' => $this->previewMode,
            'expandableInModal' => $this->expandableInModal,
            'mediumType' => $this->mediumType,
        ]);
    }

    public function render(): View
    {
        return $this->renderView('media-viewer', $this->getConfig('frontendTheme'));
    }
}
