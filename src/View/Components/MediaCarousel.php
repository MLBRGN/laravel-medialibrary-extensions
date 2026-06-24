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
        public mixed $modelOrClassName,
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

        Log::info('MediaCarousel ctor', [
            'clientToken' => $this->clientToken,
            'instanceId' => $this->instanceId,
        ]);

        if ($instanceId) {
            $this->instanceId = $instanceId;
        }

//        if ($clientToken) {
//            $this->clientToken = $clientToken;
//        }
//        if ($instanceId) {
////            Log::info('MediaCarousel: instanceId provided: ' . $instanceId);
//            $this->instanceId = $instanceId;
//        } else {
//            Log::info('MediaCarousel: no instance id');
//        }

        $this->options = $options;

        $mediaService = app(MediaService::class);

        $resolvedModel = $mediaService->resolveModelOrClassName($modelOrClassName, $dataSource);
        $model = $resolvedModel->model;

//        Log::info('MediaCarousel: resolved model: ' . json_encode($model));
        // merge into config
        $this->resolveConfig([
            'temporaryUploadMode' => $resolvedModel->temporaryUploadMode,
            'clientToken' => $this->clientToken,
        ]);

        $instanceId = $this->instanceId ?? $this->getConfig('instanceId');

//        Log::info('MediaCarousel', [
//            'id' => $this->id,
//            'instanceId' => $this->instanceId,
//        ]);

        Log::info('MediaCarousel lookup', [
            'instanceId' => $instanceId,
            'clientToken' => $this->clientToken,
//            'collections' => $this->collections,
        ]);
       $this->media = $mediaService->resolveMediaFromCollections($model, $this->collections, $instanceId, $this->clientToken, $dataSource);
//        $this->media = $this->resolveMediaFromCollections($this->collections, $instanceId);

        $this->mediaCount = $this->media->count();

        Log::info('MediaCarousel media: ' . json_encode($this->media, JSON_PRETTY_PRINT));
        Log::info('MediaCarousel mediaCount : ' . $this->mediaCount);
        $this->applyDomSuffix('crs');
    }

    public function render(): View
    {
        return $this->renderView('media-carousel', $this->getConfig('frontendTheme'));
    }
}
