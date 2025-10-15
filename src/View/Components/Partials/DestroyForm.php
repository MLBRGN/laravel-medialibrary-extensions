<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DestroyForm extends BaseComponent
{
    public ?string $mediaManagerId = '';
    //    public ?string $audioCollection;

    public function __construct(
        ?string $id,
        public Media|TemporaryUpload $medium,
        public ?string $frontendTheme,
        public ?bool $useXhr = null,
        public array $collections = [], // in image, document, youtube, video, audio
        public ?bool $disabled = false,
    ) {
        parent::__construct($id, $frontendTheme);
        $this->mediaManagerId = $id;
        $this->id = $this->id.'-destroy-form-'.$this->medium->id;
        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');
    }

    public function render(): View
    {
        return $this->getPartialView('destroy-form', $this->frontendTheme);
    }
}
