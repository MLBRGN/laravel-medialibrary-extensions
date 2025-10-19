<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DestroyForm extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public ?string $mediaManagerId = '';

    public array $config;

    public function __construct(
        ?string $id,
        public Media|TemporaryUpload $medium,
        public array $collections = [], // in image, document, youtube, video, audio
        public array $options = [],
        public ?bool $disabled = false,
    ) {
        parent::__construct($id, $this->getOption('frontendTheme'));
        $this->mediaManagerId = $id;
        $this->id = $this->id.'-destroy-form-'.$this->medium->id;

        $mediumDestroyRoute = route(mle_prefix_route('medium-destroy'), $medium);

        $this->initializeConfig([
            'frontendTheme' => $this->getOption('frontendTheme', config('media-library-extensions.frontend_theme')),
            'useXhr' => config('media-library-extensions.use_xhr'),
            'mediumDestroyRoute' => $mediumDestroyRoute,
        ]);
    }

    public function render(): View
    {
        return $this->getPartialView('destroy-form', $this->getConfig('frontendTheme'));
    }
}
