<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Services\MediaService;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManager extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    protected string $mediaUploadRoute; // upload form action route

    protected string $mediaManagerPreviewUpdateRoute; // route to update preview media when using XHR

    protected string $youtubeUploadRoute; // route to upload a YouTube video using XHR

    /** @var string Reference to the parent MediaManager's originalId */
    public string $mediaManagerId;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,
        public Media|TemporaryUpload|null $singleMedia = null,
        public array $collections = [],
        array $options = [],
        public bool $multiple = false,
        public bool $disabled = false,
        public bool $readonly = false,
        public bool $selectable = false,
        public ?string $dataSource = 'default',
    ) {

        parent::__construct($id);

        // For the root MediaManager, mediaManagerId is its own originalId
        $this->mediaManagerId = $this->originalId;
        $this->options = $options;
        $this->resolveModelOrClassName($modelOrClassName, $this->dataSource);

        // Override: enforce disabling "set-as-first" when multiple is disabled
        if (! $this->multiple) {
            $this->setOption('showSetAsFirstButton', false);
        }

        // throw exception when no media collection provided at all
        if (! $this->hasCollections()) {
            throw new Exception(__('medialibrary-extensions::messages.no_media_collections'));
        }

        // override
        if (! $this->hasCollections() || ($this->hasCollection('youtube') && collect($this->collections)->except('youtube')->filter()->isEmpty())) {
            $this->setOption('showUploadForm', false);
        }

        // override
        if (! $this->hasCollection('youtube')) {
            $this->setOption('showYouTubeUploadForm', false);
        }

        // override, don't show upload forms or "set as first" for single medium media managers
        if (! is_null($this->singleMedia)) {
            $this->setOption('showUploadForms', false);
            $this->setOption('showSetAsFirst', false);
        }

        // sync configuration with current state
        $this->syncConfigOverrides();

        $this->setDisableFormOption();
    }

    protected function setDisableFormOption(): void
    {
        if ($this->singleMedia !== null) {
            $totalMediaCount = 1;
        } else {
            $mediaService = app(MediaService::class);
            if ($this->modelOrClassName instanceof HasMedia) {
                $totalMediaCount = $mediaService->countModelMediaInCollections($this->modelOrClassName, $this->collections, $this->dataSource);
            } elseif (is_string($this->modelOrClassName)) {
                $totalMediaCount = $mediaService->countTemporaryUploadsInCollections($this->collections, $this->instanceId, $this->clientToken, $this->dataSource);
            } else {
                $totalMediaCount = 0;
            }
        }

        if ($this->multiple) {
            $maxItems = config('medialibrary-extensions.max_items_in_shared_media_collections', 10);
            $this->setOption('disableForm', $totalMediaCount >= $maxItems);
        } else {
            $this->setOption('disableForm', $totalMediaCount >= 1);
        }

        $this->resolveConfig();
    }

    protected function syncConfigOverrides(): void
    {
        // the routes, "set-as-first" and "destroy" are "medium specific" routes, so not defined here
        $this->mediaManagerPreviewUpdateRoute = route(mle_prefix_route('media-manager-preview-update'));
        $this->youtubeUploadRoute = route(mle_prefix_route('media-upload-youtube'));

        if ($this->multiple) {
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-multiple'));
            $this->setBaseId($this->getSuffixedId('mmm'));
        } else {
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-single'));
            $this->setBaseId($this->getSuffixedId('mms'));
        }

        // override hide media menu when nothing to see inside menu
        if (
            $this->getConfig('showDestroyButton') === false &&
            $this->getConfig('showSetAsFirstButton') === false &&
            $this->getConfig('showMediaEditButton') === false
        ) {
            $this->setOption('showMenu', false);
        }

        // override
        if (! $this->getOption('showUploadForm', true) && ! $this->getOption('showYouTubeUploadForm', true)) {
            $this->setOption('showUploadForms', false);
        }

        $this->resolveConfig([
            'instanceId' => $this->instanceId,
            'clientToken' => $this->clientToken,
            'temporaryUploadMode' => $this->temporaryUploadMode,
        ]);
    }

    public function render(): View
    {
        return $this->renderView('media-manager', $this->getConfig('frontendTheme'));
    }
}
