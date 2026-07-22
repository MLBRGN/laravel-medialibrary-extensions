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

    // modelOrClassName is the model that will eventually hold the media
    // Persistent mode — you have an existing model instance.
    // Temporary mode — you only know which model type will eventually own the media.
    // TODO refactor $modelOrClassName to modelReference?

    /*
     * TODO
     * The important part is what happens immediately after.

Instead of storing modelOrClassName and passing it around everywhere, normalize it once.

public ResolvedModel $resolvedModel;

public function __construct(
    public Model|string $modelOrClassName,
    ...
) {
    $this->resolvedModel = $this->mediaService
        ->resolveModelOrClassName(
            $modelOrClassName,
            $this->dataSource
        );
}
    Now the rest of the component never touches modelOrClassName again.

    TODO rename resolvedModel to MediaTarget
     A MediaTarget can naturally represent

    existing model
    future model
    model class
    temporary upload mode

    I'd also simplify ResolvedModel

Right now it contains

model
modelType
modelId
temporaryUploadMode

which means some properties are null depending on the state.

I would almost give it behavior:

$target->isTemporary();

$target->hasModel();

$target->model();

$target->modelClass();

$target->modelId();

Then callers don't care whether the original input was
     */
    public function __construct(
        ?string $id,
        // New preferred API: modelReference (camelCase => model-reference in Blade)
        public mixed $modelReference = null,
        // Backward compatibility: modelOrClassName still accepted
        public mixed $modelOrClassName = null,
        public Media|TemporaryUpload|null $singleMedia = null,
        public array $collections = [],
        array $options = [],
        public bool $multiple = false,
        public bool $disabled = false,
        public bool $readonly = false,
        public bool $selectable = false,
        public ?string $dataSource = 'default',
    ) {
        // Normalize: prefer the new prop when provided; keep both in sync for views
        if ($this->modelReference !== null) {
            $this->modelOrClassName = $this->modelReference;
        } elseif ($this->modelOrClassName !== null) {
            $this->modelReference = $this->modelOrClassName;
        }

        parent::__construct($id, $this->modelReference, $this->modelOrClassName, $dataSource);

        // Defensive sync: if the base resolved a concrete model but the legacy
        // $modelOrClassName property is still null (e.g. due to attribute name
        // mismatches during Blade binding), set it here so downstream counting
        // and mode detection behave as permanent mode.
        if ($this->model !== null && $this->modelOrClassName === null) {
            // Keep both props in sync so nested blades that bind modelReference
            // continue to operate in permanent mode.
            $this->modelOrClassName = $this->model;
            $this->modelReference = $this->modelOrClassName;
        }

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
            // IMPORTANT: For Single managers we must consider ALL configured
            // collections (including YouTube) because only one medium is
            // allowed across the entire manager, regardless of type.
            // For Multiple managers, we also count across all configured
            // collections to enforce shared limits consistently.
            $effectiveCollections = $this->collections;

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

        // Persist counts for downstream blades / sub-components
        $this->totalMediaCount = (int) ($totalMediaCount ?? 0);

        if ($this->multiple) {
            // Honor an explicitly provided per-instance max from options first,
            // then fall back to the global config. This allows demo pages (or
            // specific component instances) to tighten the limit without
            // changing global settings.
            $maxFromOptions = $this->getOption('maxMediaCount', null);
            $maxItems = (int) ($maxFromOptions ?? config('medialibrary-extensions.max_items_in_shared_media_collections', 10));

            // Persist the final max used by this instance
            $this->maxMediaCount = $maxItems;
            $this->setOption('disableForm', $this->totalMediaCount >= $maxItems);
        } else {
            $this->maxMediaCount = 1;
            $this->setOption('disableForm', $this->totalMediaCount >= 1);
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

        // Expose counters and convenience booleans through the component config
        $this->resolveConfig([
            'totalMediaCount' => $this->totalMediaCount,
            'maxMediaCount' => $this->maxMediaCount,
            'isEmpty' => $this->totalMediaCount === 0,
            'isAtMax' => $this->totalMediaCount >= $this->maxMediaCount,
            'multiple' => (bool) $this->multiple,
        ]);

        // Also propagate these values to options so that all sub-components
        // (which only receive the parent's options) can access them via
        // their own $getConfig() after resolveConfig merges options -> config.
        $this->setOption('totalMediaCount', $this->totalMediaCount);
        $this->setOption('maxMediaCount', $this->maxMediaCount);
        $this->setOption('isEmpty', $this->totalMediaCount === 0);
        $this->setOption('isAtMax', $this->totalMediaCount >= $this->maxMediaCount);
        $this->setOption('multiple', (bool) $this->multiple);
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
            ($this->getOption('showDestroyButton', $this->getConfig('showDestroyButton')) === false) &&
            ($this->getOption('showSetAsFirstButton', $this->getConfig('showSetAsFirstButton')) === false) &&
            ($this->getOption('showMediaEditButton', $this->getConfig('showMediaEditButton')) === false)
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
        return $this->renderView('media-manager', $this->getConfig('theme'));
    }
}
