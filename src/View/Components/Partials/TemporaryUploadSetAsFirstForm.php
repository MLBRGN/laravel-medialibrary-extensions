<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class TemporaryUploadSetAsFirstForm extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public ?string $targetMediaCollection = null;

    public ?string $mediaManagerId = '';

    public array $config;

    public function __construct(
        ?string $id,
        public Collection $media,
        public TemporaryUpload $medium,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public array $options = [],
        public array $collections = [],
        public ?bool $readonly = false,
        public ?bool $disabled = false,
    ) {
        parent::__construct($id);

        $this->mediaManagerId = $this->id;
        $this->id = $this->id.'-destroy-form-'.$this->medium->id;
        $this->targetMediaCollection = $medium->collection_name;

        $this->resolveModelOrClassName($modelOrClassName);

        $this->initializeConfig([
//            'frontendTheme' => $this->getOption('frontendTheme', config('media-library-extensions.frontend_theme')),
//            'useXhr' => config('media-library-extensions.use_xhr'),
        ]);
    }

    public function render(): View
    {
        return $this->getPartialView('temporary-upload-set-as-first-form', $this->getConfig('frontendTheme'));
    }
}
