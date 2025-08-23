<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\Contracts\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageEditorModal extends BaseComponent
{
    public array $config = [];

    public string $saveUpdatedMediumRoute;

    public HasMedia|null $model = null;

    public ?string $modelType = null;

    public mixed $modelId = null;

    public bool $temporaryUpload = false;

    public ?string $mediaManagerId = null;

    public function __construct(
        public HasMedia|string $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload $medium,
        public string $id,
        public string $initiatorId,
        public string $title = 'no title',// TODO do i want this?
        public ?string $frontendTheme = null,
        public ?string $imageCollection = '',
        public ?string $documentCollection = '',
        public ?string $youtubeCollection = '',
        public ?string $videoCollection = '',
        public ?string $audioCollection = '',
        public ?bool $useXhr = true,
    ) {
        parent::__construct($id, $frontendTheme);

//        $this->id = $this->id.'-image-editor-modal-'.$medium->id;
        $this->mediaManagerId = $this->id;
        $this->id = $this->id.'-iem-'.$medium->id;

        if ($modelOrClassName instanceof HasMedia) {
            $this->model = $modelOrClassName;
            $this->modelType = $modelOrClassName->getMorphClass();
            $this->modelId = $modelOrClassName->getKey();
            $this->saveUpdatedMediumRoute = route(mle_prefix_route('save-updated-medium'), $medium);
        } elseif (is_string($modelOrClassName)) {
            $this->model = null;
            $this->modelType = $modelOrClassName;
            $this->modelId = null;
            $this->temporaryUpload = true;
            $this->saveUpdatedMediumRoute = route(mle_prefix_route('save-updated-temporary-upload'), $medium);
        } else {
            throw new Exception('model-or-class-name must be either a HasMedia model or a string representing the model class');
        }

        // TODO can't i just read the whole config object from hidden input?
        // Config array passed to view
        $this->config = [
            'id' => $this->id,
            'initiator_id' => $this->initiatorId,
            'media_manager_id' => $this->mediaManagerId,
            'model_type' => $this->modelType,
            'model_id' => $this->modelId,
            'medium_id' => $this->medium->id,
            'collection' => $this->medium->collection_name,
            'csrf_token' => csrf_token(),
            'save_updated_medium_route' => $this->saveUpdatedMediumRoute,
            'temporary_upload' => $this->temporaryUpload,
            'image_collection' => $this->imageCollection,
            'document_collection' => $this->documentCollection,
            'youtube_collection' => $this->youtubeCollection,
            'video_collection' => $this->videoCollection,
            'audio_collection' => $this->audioCollection,
            'use_xhr' => $this->useXhr,
        ];
    }

    public function render(): View
    {
        if ($this->temporaryUpload) {
            return $this->getView('image-editor-modal-temporary-upload', $this->frontendTheme);
        }

        return $this->getView('image-editor-modal', $this->frontendTheme);
    }
}
