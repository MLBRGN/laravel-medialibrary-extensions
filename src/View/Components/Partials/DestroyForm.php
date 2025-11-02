<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DestroyForm extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public ?string $mediaManagerId = '';

    public array $config;

    public string $mediumDestroyRoute;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload $medium,
        public Media|TemporaryUpload|null $singleMedium = null,
        public array $collections = [],
        public array $options = [],
        public ?bool $disabled = false,
    ) {
        parent::__construct($id);

        $this->resolveModelOrClassName($modelOrClassName);

        $this->mediaManagerId = $id;
        $this->id = $this->id.'-destroy-form-'.$this->medium->id;

        if ($this->temporaryUploadMode) {
            $mediumDestroyRoute = route(
                mle_prefix_route('temporary-upload-destroy'),
                ['temporaryUpload' => $medium->id] // 👈 exact match to route parameter
            );
        } else {
            $mediumDestroyRoute = route(
                mle_prefix_route('medium-destroy'),
                ['media' => $medium->id]
            );
        }

        $this->mediumDestroyRoute = $mediumDestroyRoute;

        $this->initializeConfig();

        //        dump($this->config);
        $this->setConfig('mediumDestroyRoute', $this->mediumDestroyRoute);
    }

    public function render(): View
    {
        return $this->getPartialView('destroy-form', $this->getConfig('frontendTheme'));
    }
}
