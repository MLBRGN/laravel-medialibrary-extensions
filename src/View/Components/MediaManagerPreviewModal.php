<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class MediaManagerPreviewModal extends BaseMediaManager
{
    public MediaCollection $mediaItems;

    public function __construct(
        public ?Model $model,
        public ?string $mediaCollection,
        public string $title,
        public string $sizeClass = 'modal-almost-fullscreen',
        public string $id = '',
        public ?string $frontendTheme = null
    ) {
        parent::__construct($id, $frontendTheme);

        $this->mediaItems = $model->getMedia($mediaCollection);
        $this->frontend = $frontendTheme ?? config('media-library-extensions.frontend_theme', 'plain');

    }

    public function render(): View
    {
        return $this->getView('media-manager-preview-modal');
    }
}
