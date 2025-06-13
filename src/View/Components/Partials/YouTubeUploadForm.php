<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;

class YouTubeUploadForm extends BaseComponent
{

    public bool $mediaPresent = false;

    public function __construct(

        public ?HasMedia $model,
        public ?string $youtubeCollection,
        public string $id,
        public ?string $frontendTheme,
    ) {
        parent::__construct($id, $frontendTheme);

        $this->mediaPresent = $this->model && $this->youtubeCollection
            ? $this->model->hasMedia($this->youtubeCollection)
            : false;
    }

    public function render(): View
    {
        return $this->getPartialView('youtube-upload-form', $this->theme);
    }
}
