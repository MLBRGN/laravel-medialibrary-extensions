<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SetAsFirstForm extends BaseComponent
{
    public function __construct(
        public Collection $media,
        public Media $medium,
        public string $id,
        public ?string $frontendTheme,
        public ?bool $useXhr = null,
        public string $mediaCollection = '',
//        public string $youtubeCollection = '',
//        public string $documentCollection = '',
        public bool $setAsFirstEnabled = false,
        public ?HasMedia $model,
    ) {
        parent::__construct($id, $frontendTheme);
    }

    public function render(): View
    {
        $this->useXhr = !is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');
        return $this->getPartialView('set-as-first-form', $this->theme);
    }
}
