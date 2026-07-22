<?php

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Preview;

use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseMediaComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaPreviews extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    public Collection $media;

    public function __construct(
        ?string $id,
        // New preferred prop; legacy supported for BC
        public mixed $modelReference = null,
        public mixed $modelOrClassName = null,// either a modal that implements HasMedia or it's class name
        public array $collections = [],
        array $options = [],
        public Media|TemporaryUpload|null $singleMedia = null, // when provided, skip collection lookups and use this medium
        public bool $multiple = false,
        public bool $disabled = false,
        public bool $readonly = false,
        public bool $selectable = false,
        public string $instanceId = '',
        public ?string $dataSource = 'default',
        ?string $clientToken = null,
    ) {
        // Normalize both props for downstream blades
        if ($this->modelReference !== null) {
            $this->modelOrClassName = $this->modelReference;
        } elseif ($this->modelOrClassName !== null) {
            $this->modelReference = $this->modelOrClassName;
        }

        parent::__construct($id, $this->modelReference, $this->modelOrClassName, $dataSource);

        // Priority:
        // 1. Explicitly passed $instanceId (e.g. from XHR or tests)
        // 2. Derive from $id (baseId)
        if (! empty($instanceId)) {
            $this->instanceId = $instanceId;
        }

        if ($clientToken) {
            $this->clientToken = $clientToken;
        }

        $this->options = $options;

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
                            $temps = TemporaryUpload::getForCurrentClient($collectionName, $this->instanceId, $dataSource, $this->clientToken);

                            return $temps;
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

    protected function domIdSuffix(): string
    {
        return 'media-previews';
    }

    public function render(): View
    {
        return $this->renderView('preview.media-previews', $this->getConfig('theme'));
    }
}
