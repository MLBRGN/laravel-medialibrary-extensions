<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;

class TemporaryUploadSetAsFirstForm extends BaseComponent
{
    public function __construct(
        public Collection $media,
        public TemporaryUpload $medium,
        public string $id,
        public ?string $frontendTheme,
        public ?bool $useXhr = null,
        public ?string $targetMediaCollection = null,
        public ?string $imageCollection = '',
        public ?string $documentCollection = '',
        public ?string $youtubeCollection = '',
        public ?string $videoCollection = '',
        public ?string $audioCollection = '',
        public bool $setAsFirstEnabled = false,
        //        public ?HasMedia $model,
    ) {
        parent::__construct($id, $frontendTheme);
    }

    public function render(): View
    {
        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

        return $this->getPartialView('temporary-upload-set-as-first-form', $this->frontendTheme);
    }
}
