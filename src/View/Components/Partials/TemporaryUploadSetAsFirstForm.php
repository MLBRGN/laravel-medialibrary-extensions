<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class TemporaryUploadSetAsFirstForm extends BaseComponent
{
    use ResolveModelOrClassName;

    public ?string $targetMediaCollection = null;

    public ?string $mediaManagerId = '';

    public function __construct(
        ?string $id,
        public Collection $media,
        public TemporaryUpload $medium,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public ?string $frontendTheme,
        public ?bool $useXhr = null,
        public array $collections = [], // in image, document, youtube, video, audio
        public bool $showSetAsFirstButton = false,
        public ?bool $readonly = false,
        public ?bool $disabled = false,
    ) {
        parent::__construct($id, $frontendTheme);

        $this->mediaManagerId = $this->id;
        $this->id = $this->id.'-destroy-form-'.$this->medium->id;
        $this->targetMediaCollection = $medium->collection_name;
        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');
        $this->resolveModelOrClassName($modelOrClassName);

    }

    public function render(): View
    {

        return $this->getPartialView('temporary-upload-set-as-first-form', $this->frontendTheme);
    }
}
