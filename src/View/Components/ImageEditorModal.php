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

    public ?string $mediaManagerId = null;

    public string $minimalDimensions;

    public string $maximalDimensions;

    public string $forcedAspectRatio;

    public function __construct(
        string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload $medium,
        public Media|TemporaryUpload|null $singleMedia,
        public array $collections,
        array $options,
        public string $initiatorId,
        public string $title = 'no title',// TODO do i want this?
        public bool $disabled = false,
        public ?string $dataSource = 'default'
    ) {
        parent::__construct($id);
        $this->options = $options;

        $this->mediaManagerId = $this->originalId;
        $this->setBaseId($this->getSuffixedId('iem-'.$medium->id));

        $resolvedModel = $this->mediaService->resolveModelOrClassName($modelOrClassName, $this->dataSource);
        $this->setModelProperties($resolvedModel);

        $this->storeUpdatedMediaRoute = $this->temporaryUploadMode ? route(mle_prefix_route('save-updated-temporary-upload'),
            $medium) : route(mle_prefix_route('save-updated-media'), $medium);

        // TODO look at this
        $this->resolveConfig([
            'initiatorId' => $this->initiatorId,
            'id' => $this->id,
            'mediaManagerId' => $this->mediaManagerId,
            'modelType' => $this->modelType,
            'modelId' => $this->modelId,
            'mediumId' => $this->medium->id,
            'collection' => $this->medium->collection_name,
            'storeUpdatedMediaRoute' => $this->storeUpdatedMediaRoute,
            'collections' => $this->collections,
            'dataSource' => $this->dataSource,
        ]);

        $this->minimalDimensions = config('medialibrary-extensions.min_image_width').'x'.config('medialibrary-extensions.min_image_height');
        $this->maximalDimensions = config('medialibrary-extensions.max_image_width').'x'.config('medialibrary-extensions.max_image_height');
        $this->forcedAspectRatio = $this->model?->getRequiredMediaAspectRatioString($medium)
            ?? config('medialibrary-extensions.default_forced_aspect_ratio');
    }

    public function render(): View
    {
        if ($this->temporaryUploadMode) {
            return $this->getView('image-editor-modal-temporary-upload', $this->getConfig('frontendTheme'));
        }

        return $this->getView('image-editor-modal', $this->getConfig('frontendTheme'));
    }
}
