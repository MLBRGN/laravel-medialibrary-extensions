<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SetAsFirstForm extends BaseComponent
{
    use ResolveModelOrClassName;

    public ?string $targetMediaCollection = null;

    public ?string $mediaManagerId = '';

    public function __construct(
        ?string $id,
        public Collection $media,
        public Media|TemporaryUpload $medium,// TODO should never be temporary upload, but then I get error on demo pages?
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public array $options = [],
        public ?string $frontendTheme,// TODO in options?
        public ?bool $useXhr,// TODO in options?
        public array $collections, // in image, document, youtube, video, audio
        public bool $showSetAsFirstButton,// TODO in options?
        public ?bool $disabled = false,
    ) {
        parent::__construct($id, $frontendTheme);

        $this->mediaManagerId = $this->id;
        $this->id = $this->id.'-set-as-first-form-'.$this->medium->id;

        $this->targetMediaCollection = $medium->collection_name;

        $this->resolveModelOrClassName($modelOrClassName);
    }

    public function render(): View
    {
        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

        return $this->getPartialView('set-as-first-form', $this->frontendTheme);
    }
}
