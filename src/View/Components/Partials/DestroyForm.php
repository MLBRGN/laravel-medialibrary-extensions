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

    /** Identity of the parent MediaManager (logical ID, not suffixed) */
    public string $mediaManagerId;

    /** Identity of the parent MediaManager (DOM ID, potentially suffixed) */
    public string $mediaManagerDomId;

    public string $mediaDestroyRoute;

    public function __construct(
        ?string $id,
        ?string $mediaManagerDomId,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload $medium,
        public Media|TemporaryUpload|null $singleMedia = null,
        public array $collections = [],
        array $options = [],
        public ?bool $disabled = false,
        public string $instanceId = '',
        public ?string $dataSource = 'default',
        ?string $mediaManagerId = null,
    ) {
        parent::__construct($id, $this->modelOrClassName, $dataSource);

        $this->mediaManagerId = $mediaManagerId ?? $this->id;
        $this->mediaManagerDomId = $mediaManagerDomId ?? $this->getDomId();

        // Ensure instanceId is derived from the mediaManagerId (the parent manager's stable identity)
        $this->instanceId = InstanceManager::getInstanceId($this->mediaManagerId);

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

    protected function domIdSuffix(): string {
        return 'destroy-form-'.$this->medium->id;
    }

    public function render(): View
    {
        return $this->renderView('destroy-form', $this->getConfig('frontendTheme'), true);
    }
}
