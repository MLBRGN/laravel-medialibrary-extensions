<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Illuminate\Contracts\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageEditorModal extends BaseComponent
{
    use ResolveModelOrClassName;
    use InteractsWithOptionsAndConfig;

    public array $config = [];

    public string $saveUpdatedMediumRoute;

    public ?string $mediaManagerId = null;

    public function __construct(
        string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload $medium,
        public array $collections = [], // in image, document, youtube, video, audio
        public array $options = [],
        public string $initiatorId,
        public string $title = 'no title',// TODO do i want this?
        public bool $disabled = false,
    ) {
        parent::__construct($id);

        $this->mediaManagerId = $this->id;
        $this->id = $this->id.'-iem-'.$medium->id;

        $this->resolveModelOrClassName($modelOrClassName);

        $this->saveUpdatedMediumRoute = $this->temporaryUploadMode ? route(mle_prefix_route('save-updated-temporary-upload'), $medium) : route(mle_prefix_route('save-updated-medium'), $medium);

        // TODO look at this
        $this->initializeConfig([
            'initiatorId' => $this->initiatorId,
//            'frontendTheme' => $this->frontendTheme,
//            'useXhr' => $this->options['useXhr'] ?? config('media-library-extensions.use_xhr', true),
            'id' => $this->id,
            'mediaManagerId' => $this->mediaManagerId,
            'modelType' => $this->modelType,
            'modelId' => $this->modelId,
            'mediumId' => $this->medium->id,
            'collection' => $this->medium->collection_name,
//            'csrfToken' => csrf_token(),
            'saveUpdatedMediumRoute' => $this->saveUpdatedMediumRoute,
//            'temporaryUploadMode' => $this->temporaryUploadMode,
            'collections' => $this->collections,
        ]);
    }

    public function render(): View
    {
        if ($this->temporaryUploadMode) {
            return $this->getView('image-editor-modal-temporary-upload', $this->getConfig('frontendTheme'));
        }

        return $this->getView('image-editor-modal', $this->getConfig('frontendTheme'));
    }
}
