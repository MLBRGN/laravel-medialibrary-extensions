<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class TemporaryUploadDestroyForm extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public ?string $mediaManagerId = '';

    public function __construct(

        ?string $id,
        public TemporaryUpload $medium,
        public Media|TemporaryUpload|null $singleMedium = null,
        public array $collections = [],
        public array $options = [],
        public ?bool $readonly = false,
        public ?bool $disabled = false,
    ) {
        parent::__construct($id);

        $this->mediaManagerId = $this->id;
        $this->id = $this->id.'-destroy-form-'.$this->medium->id;

        $this->initializeConfig([
//            'frontendTheme' => $this->getOption('frontendTheme', config('media-library-extensions.frontend_theme')),
//            'useXhr' => config('media-library-extensions.use_xhr'),
        ]);
    }

    public function render(): View
    {
        return $this->getPartialView('temporary-upload-destroy-form', $this->getConfig('frontendTheme'));
    }
}
