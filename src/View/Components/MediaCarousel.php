<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaCarousel extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public Collection $media;

    public int $mediaCount;

    public string $previewerId = '';

    public function __construct(
        ?string $id,
        // New preferred prop; legacy supported via sync below
        public mixed $modelReference = null,
        public mixed $modelOrClassName = null,
        public Media|TemporaryUpload|null $singleMedia = null, // when provided, skip collection lookups and use this medium
        public ?array $collections = [],
        public bool $expandableInModal = true,
        array $options = [],
        public bool $inModal = false, // TODO used anywhere?
        public bool $previewMode = true, // should the media-viewer be in preview mode (no autoplay, no document loading or not)
        ?string $instanceId = null,
        public ?string $dataSource = 'default',
        ?string $clientToken = null,
    ) {
        // Normalize for BC
        if ($this->modelReference !== null) {
            $this->modelOrClassName = $this->modelReference;
        } elseif ($this->modelOrClassName !== null) {
            $this->modelReference = $this->modelOrClassName;
        }

        parent::__construct($id);

        if ($instanceId) {
            $this->instanceId = $instanceId;
        }

        if ($clientToken) {
            $this->clientToken = $clientToken;
        }

        $this->options = $options;

        $mediaService = app(MediaService::class);

        $resolvedModel = $mediaService->resolveModelOrClassName($this->modelOrClassName, $dataSource);
        $model = $resolvedModel->model;

        // merge into config
        $this->resolveConfig([
            'temporaryUploadMode' => $resolvedModel->temporaryUploadMode,
            'clientToken' => $this->clientToken,
        ]);

        $this->media = $mediaService->resolveMediaFromCollections($model, $this->collections, $instanceId, $this->clientToken, $dataSource, true);

        $this->mediaCount = $this->media->count();

    }

    protected function domIdSuffix(): string
    {
        return 'crs';
    }

    public function render(): View
    {
        return $this->renderView('media-carousel', $this->getConfig('theme'));
    }
}
