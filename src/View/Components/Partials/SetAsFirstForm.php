<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SetAsFirstForm extends BaseComponent
{
    public ?string $targetMediaCollection = null;

    public ?string $mediaManagerId = '';

    public function __construct(
        ?string $id,
        public Collection $media,
        public Media|TemporaryUpload $medium,
        public ?string $frontendTheme,
        public ?bool $useXhr,
        public array $collections, // in image, document, youtube, video, audio
        public bool $showSetAsFirstButton,
        public ?HasMedia $model,
        public ?bool $disabled = false,
    ) {
        parent::__construct($id, $frontendTheme);

        $this->mediaManagerId = $this->id;
        $this->id = $this->id.'-set-as-first-form-'.$this->medium->id;

        $this->targetMediaCollection = $medium->collection_name;
    }

    public function render(): View
    {
        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

        return $this->getPartialView('set-as-first-form', $this->frontendTheme);
    }
}
