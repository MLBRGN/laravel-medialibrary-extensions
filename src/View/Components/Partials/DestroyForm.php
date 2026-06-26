<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseMediaComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DestroyForm extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

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
        public ?string $dataSource = 'default',
        ?string $clientToken = null,
    ) {
        parent::__construct($id, $this->modelOrClassName, $dataSource);

        // Ensure instanceId is derived from the Base ID
        if (empty($this->instanceId)) {
            $this->instanceId = InstanceManager::getInstanceId($this->id);
        }

        if ($clientToken) {
            $this->clientToken = $clientToken;
        }

        $this->options = $options;

        if ($this->medium instanceof Media && is_null($this->modelId)) {
            $this->modelId = $this->medium->model_id;
        }

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

    protected function domIdSuffix(): string
    {
        return 'destroy-form-'.$this->medium->id;
    }

    public function render(): View
    {
        return $this->renderView('destroy-form', $this->getConfig('frontendTheme'), true);
    }
}
