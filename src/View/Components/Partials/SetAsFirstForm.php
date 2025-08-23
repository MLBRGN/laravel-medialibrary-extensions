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
    public ?string $targetMediaCollection = null;
    public ?string $mediaManagerId = '';

    public function __construct(
        public Collection $media,
        public Media $medium,
        public string $id,
        public ?string $frontendTheme,
        public ?bool $useXhr,
        public ?string $imageCollection = '',
        public ?string $documentCollection = '',
        public ?string $youtubeCollection = '',
        public ?string $videoCollection = '',
        public ?string $audioCollection = '',
        public bool $setAsFirstEnabled,
        public ?HasMedia $model,
    ) {
        parent::__construct($id, $frontendTheme);

        $this->mediaManagerId = $this->id;
        $this->id = $this->id . '-set-as-first-form-' . $this->medium->id;

        $this->targetMediaCollection = $medium->collection_name;
    }

    public function render(): View
    {
        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

        return $this->getPartialView('set-as-first-form', $this->frontendTheme);
    }
}
