<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class TemporaryUploadDestroyForm extends BaseComponent
{
    public ?string $mediaManagerId = '';

    public function __construct(

        public TemporaryUpload $medium,
        ?string $id,
        public array $options = [],
        public ?string $frontendTheme,// TODO in options?
        public ?bool $useXhr = null,// TODO in options?
        public array $collections = [], // in image, document, youtube, video, audio
        public ?bool $readonly = false,
        public ?bool $disabled = false,
    ) {
        parent::__construct($id, $frontendTheme);

        $this->mediaManagerId = $this->id;
        $this->id = $this->id.'-destroy-form-'.$this->medium->id;
        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');
    }

    public function render(): View
    {
        return $this->getPartialView('temporary-upload-destroy-form', $this->frontendTheme);
    }
}
