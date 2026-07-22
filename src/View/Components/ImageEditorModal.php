<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageEditorModal extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    public string $storeUpdatedMediaRoute;

    public string $minimalDimensions;

    public string $maximalDimensions;

    public string $forcedAspectRatio;

    public function __construct(
        string $id,
        // Legacy/BC: keep modelOrClassName in original position
        public mixed $modelOrClassName = null, // either a model implementing HasMedia or its class name
        public Media|TemporaryUpload $medium,
        public Media|TemporaryUpload|null $singleMedia = null,
        public array $collections,
        array $options,
        public string $title = 'no title', // optional title
        public bool $disabled = false,
        public ?string $dataSource = 'default',
        // New preferred prop (placed at end to preserve BC with named/positional args)
        public mixed $modelReference = null,
    ) {
        // Normalize both props for downstream blades
        if ($this->modelReference !== null) {
            $this->modelOrClassName = $this->modelReference;
        } elseif ($this->modelOrClassName !== null) {
            $this->modelReference = $this->modelOrClassName;
        }

        parent::__construct($id, $this->modelReference, $this->modelOrClassName, $dataSource);

        $this->options = $options;

        $this->storeUpdatedMediaRoute = $this->temporaryUploadMode ? route(mle_prefix_route('save-updated-temporary-upload'),
            $medium) : route(mle_prefix_route('save-updated-media'), $medium);

        // TODO look at this
        $this->resolveConfig([
            'id' => $this->id,
            'baseId' => $this->id,
            'modelType' => $this->modelType,
            'modelId' => $this->modelId,
            'mediumId' => $this->medium->id,
            'collection' => $this->medium->collection_name,
            'storeUpdatedMediaRoute' => $this->storeUpdatedMediaRoute,
            'collections' => $this->collections,
            'dataSource' => $this->dataSource,
            // Provide CSRF directly to the modal config to make tests and SSR robust
            'csrfToken' => csrf_token(),
            // Preserve current theme for any XHR/refresh flows that need it
            'theme' => $this->getConfig('theme'),
            // Normalize as strings to satisfy validation Rule::in(['true','false'])
            'temporaryUploadMode' => $this->temporaryUploadMode ? 'true' : 'false',
        ]);

        $this->minimalDimensions = config('medialibrary-extensions.min_image_width').'x'.config('medialibrary-extensions.min_image_height');
        $this->maximalDimensions = config('medialibrary-extensions.max_image_width').'x'.config('medialibrary-extensions.max_image_height');
        $this->forcedAspectRatio = $this->model?->getRequiredMediaAspectRatioString($medium)
            ?? config('medialibrary-extensions.default_forced_aspect_ratio');
    }

    protected function domIdSuffix(): string
    {
        return 'iem-'.$this->medium->id;
    }

    public function render(): View
    {
        if ($this->temporaryUploadMode) {
            return $this->getView('image-editor-modal-temporary-upload', $this->getConfig('theme'));
        }

        return $this->getView('image-editor-modal', $this->getConfig('theme'));
    }
}
