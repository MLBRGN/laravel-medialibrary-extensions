<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaModelResolver;
use Mlbrgn\MediaLibraryExtensions\Services\MediaRetriever;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaCarousel extends BaseComponent
{
    use InteractsWithOptionsAndConfig;

    public Collection $media;

    public int $mediaCount;

    public bool $temporaryUploadMode = false;

    public string $previewerId = '';

    public function __construct(
        string $id,
        public mixed $modelReference,
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
        parent::__construct($id);

        if ($instanceId) {
            $this->instanceId = $instanceId;
        }

        if ($clientToken) {
            $this->clientToken = $clientToken;
        }

        $this->options = $options;

        $mediaModelResolver = app(MediaModelResolver::class);

        $resolvedModel = $mediaModelResolver->resolveModelReference($modelReference, $dataSource);
        $model = $resolvedModel->model;
        $this->temporaryUploadMode = $resolvedModel->temporaryUploadMode;

        // merge into config
        $this->resolveConfig([
            'temporaryUploadMode' => $this->temporaryUploadMode,
            'clientToken' => $this->clientToken,
        ]);

        $includeTemporaryUploads = $this->getOption('includeTemporaryUploads', $this->temporaryUploadMode);

        $mediaRetriever = app(MediaRetriever::class);
        $this->media = $mediaRetriever->resolveMediaFromCollections($model, $this->collections, $instanceId, $this->clientToken, $dataSource, $includeTemporaryUploads);

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
