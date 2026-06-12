<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Preview;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPreviews extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public Collection $media;

    public function __construct(
        ?string $id,
        public ?string $mediaManagerId = null,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public array $collections = [],
        array $options = [],
        public Media|TemporaryUpload|null $singleMedia = null, // when provided, skip collection lookups and use this medium
        public bool $multiple = false,
        public bool $disabled = false,
        public bool $readonly = false,
        public bool $selectable = false,
        public string $instanceId = '',
        public ?string $dataSource = null,
        public ?string $sessionId = null,
    ) {
        parent::__construct($id);

        $this->mediaManagerId = $mediaManagerId ?? $this->originalId;

        // Ensure instanceId is derived from the mediaManagerId (the parent manager's identity)
        // unless it was explicitly provided (e.g. from an XHR request or a test)
        if (empty($instanceId)) {
            $this->instanceId = \Mlbrgn\MediaLibraryExtensions\Support\InstanceManager::getInstanceId($this->mediaManagerId);
        } else {
            $this->instanceId = $instanceId;
        }

        $this->options = $options;
        $this->sessionId = $sessionId ?: (request()->hasSession() ? request()->session()->getId() : session()->getId());

        $this->resolveModelOrClassName($modelOrClassName);

        if (isset($options['temporaryUploadMode'])) {
            $this->temporaryUploadMode = (bool) $options['temporaryUploadMode'];
        }

        $this->media = collect();

        // CASE 1: If a single medium is provided, use only that.
        if ($this->singleMedia instanceof Media || $this->singleMedia instanceof TemporaryUpload) {
            $this->media->push($this->singleMedia);
        } else {
            $this->media = collect($this->collections)
                ->filter(fn ($collectionName
                ) => ! is_null($collectionName) && $collectionName !== '') // remove null or empty
                ->flatMap(function (?string $collectionName, string $collectionType) use ($dataSource) {
                    if ($this->temporaryUploadMode) {
                        if (! empty($collectionName)) {
                            return TemporaryUpload::getForCurrentSession($collectionName, $this->instanceId, $dataSource, $this->sessionId);
                        }
                    }

                    if ($this->model) {
                        return $this->model->getMedia($collectionName);
                    }

                    return [];
                })
                ->sortBy(fn ($m) => $m->getCustomProperty('priority', PHP_INT_MAX))
                ->values();
        }

        // TODO use Collection or MediaCollection?
        // $this->mediaItems = MediaCollection::make($allMedia);
        // $this->media = $this->mediaItems;
        // $this->mediaCount = $this->mediaItems->count();

        $this->resolveConfig([
            'temporaryUploadMode' => $this->temporaryUploadMode,
        ]);

        // sync configuration with current state
        $this->syncConfigOverrides();
    }

    protected function syncConfigOverrides(): void
    {
        // override hide media menu when nothing to see inside menu
        if (
            $this->getConfig('showDestroyButton') === false &&
            $this->getConfig('showSetAsFirstButton') === false &&
            $this->getConfig('showMediaEditButton') === false
        ) {
            $this->setOption('showMenu', false);
        }

        $this->resolveConfig([
            'temporaryUploadMode' => $this->temporaryUploadMode,
        ]);
    }

    public function render(): View
    {
        return $this->renderView('preview.media-previews', $this->getConfig('frontendTheme'));
    }
}
