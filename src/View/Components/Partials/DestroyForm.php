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

    //    public array $config;

    public string $mediaDestroyRoute;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload $medium,
        public Media|TemporaryUpload|null $singleMedia = null,
        public array $collections = [],
        array $options = [],
        public ?bool $disabled = false,
        public string $instanceId = '',
        public ?string $dataSource = null
    ) {
        parent::__construct($id);
        if ($instanceId) {
            $this->instanceId = $instanceId;
        }
        $this->options = $options;

        $this->resolveModelOrClassName($modelOrClassName);

        if ($this->medium instanceof Media && is_null($this->modelId)) {
            $this->modelId = $this->medium->model_id;
        }

        $this->mediaManagerId = $this->originalId;
        $this->setBaseId($this->getSuffixedId('destroy-form-'.$this->medium->id));

        if ($this->temporaryUploadMode) {
            $mediaDestroyRoute = route(
                mle_prefix_route('destroy-temporary-upload'),
                ['temporaryUploadId' => $medium->id]
            );
        } else {
            $mediaDestroyRoute = route(
                mle_prefix_route('destroy-media'),
                ['mediaId' => $medium->id]
            );
        }

        $this->mediaDestroyRoute = $mediaDestroyRoute;

        $this->resolveConfig();

        $this->setConfig('routes.mediaDestroy', $this->mediaDestroyRoute);
    }

    public function render(): View
    {
        return $this->renderView('destroy-form', $this->getConfig('frontendTheme'), true);
    }
}
