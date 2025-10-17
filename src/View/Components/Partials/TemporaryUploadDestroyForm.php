<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class TemporaryUploadDestroyForm extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public ?string $mediaManagerId = '';

    public function render(): View
    {
        return $this->getPartialView('temporary-upload-destroy-form', $this->frontendTheme);
    }

    public function __construct(

        public TemporaryUpload $medium,
        ?string $id,
        public array $options = [],
        public array $collections = [], // in image, document, youtube, video, audio
        public ?bool $readonly = false,
        public ?bool $disabled = false,
        public ?string $frontendTheme,// TODO in options?
        public ?bool $useXhr = null,// TODO in options?
    ) {
        parent::__construct($id, $frontendTheme);

        $this->mediaManagerId = $this->id;
        $this->id = $this->id.'-destroy-form-'.$this->medium->id;
        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

        $this->initializeConfig([
            'frontendTheme' => $this->getOption('frontendTheme', config('media-library-extensions.frontend_theme')),
            'useXhr' => config('media-library-extensions.use_xhr'),
        ]);
    }
}
