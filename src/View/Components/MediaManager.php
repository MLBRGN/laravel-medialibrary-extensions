<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManager extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    protected ?string $domIdSuffix = 'mmm';

    protected string $mediaUploadRoute; // upload form action route

    protected string $mediaManagerPreviewUpdateRoute; // route to update preview media when using XHR

    protected string $youtubeUploadRoute; // route to upload a YouTube video using XHR

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

        parent::__construct($id, $this->modelOrClassName, $dataSource);

        $this->options = $options;

        // Enforce option: do not allow "Set as first" when not multiple
        // This aligns backend config with tests that expect the option to be false for Single managers.
        if ($this->multiple === false) {
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

        // override, don't show upload forms for single medium media managers
        if (! is_null($this->singleMedia)) {
            $this->setOption('showUploadForms', false);
        }

        // sync configuration with the current state
        $this->syncConfigOverrides();

        $this->setDisableFormOption();

    }

    protected function setDisableFormOption(): void
    {
        // CASE 1: provided with a single medium (permanent or temporary)
        if ($this->singleMedia !== null) {
            $totalMediaCount = 1;
        } else {
            // Determine effective collections to count.
            // For Single managers we should only consider the target collection,
            // not sum across every non-empty collection in the map. This avoids
            // incorrectly disabling the form when another collection has items.
            $effectiveCollections = $this->collections;

            if (! $this->multiple) {
                // Keep only the first non-empty collection value
                $nonEmpty = array_values(array_filter($this->collections, fn ($v) => filled($v)));
                if (! empty($nonEmpty)) {
                    $effectiveCollections = [$nonEmpty[0]];
                }
            }

            // CASE 2: Permanent mode (model instance provided)
            if ($this->modelOrClassName instanceof HasMedia) {
                $totalMediaCount = $this->mediaService->countModelMediaInCollections($this->modelOrClassName, $effectiveCollections, $this->dataSource);
            }
            // CASE 3: Temporary mode (class name string provided)
            elseif (is_string($this->modelOrClassName)) {
                $totalMediaCount = $this->mediaService->countTemporaryUploadsInCollections(
                    $effectiveCollections,
                    $this->instanceId,
                    $this->clientToken,
                    $this->dataSource
                );
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

        // Structured debug to help diagnose cross-scope counting
//        Log::debug('mle.mediaManager.setDisableFormOption', [
//            'component' => static::class,
//            'id' => $this->id,
//            'instanceId' => $this->instanceId,
//            'dataSource' => $this->dataSource,
//            'clientToken' => $this->clientToken ? substr($this->clientToken, 0, 4).'…'.substr($this->clientToken, -4) : null,
//            'multiple' => $this->multiple,
//            'effectiveCollections' => $effectiveCollections ?? $this->collections,
//            'totalMediaCount' => $totalMediaCount,
//            'disableForm' => (bool) $this->getOption('disableForm'),
//        ]);

        $this->resolveConfig();
    }

    protected function syncConfigOverrides(): void
    {
        // the routes, "set-as-first" and "destroy" are "medium specific" routes, so not defined here
        $this->mediaManagerPreviewUpdateRoute = route(mle_prefix_route('media-manager-preview-update'));
        $this->youtubeUploadRoute = route(mle_prefix_route('media-upload-youtube'));

        if ($this->multiple) {
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-multiple'));
        } else {
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-single'));
        }
        //        $this->mediaManagerDomId = $this->domId;

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

    // Note: must be here and not in MediaManagerSingle and MediaManagerMultiple classes. MediaManager can be called on its own.
    protected function domIdSuffix(): string
    {
        if ($this->multiple) {
            return 'mmm';
        } else {
            return 'mms';
        }
    }

    public function render(): View
    {
        return $this->renderView('media-manager', $this->getConfig('frontendTheme'));
    }
}
