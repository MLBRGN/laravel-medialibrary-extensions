<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageEditorForm extends BaseComponent
{
    public HasMedia|null $model = null;

    public ?string $modelType = null;

    public mixed $modelId = null;

    public bool $temporaryUpload = false;
    public string $saveUpdatedMediumRoute;

    public function __construct(
        public HasMedia|string $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload $medium,
        public string $id,
        public ?string $frontendTheme,
        public string $initiatorId,
        public ?bool $useXhr = null,
        public ?string $imageCollection = '',
        public ?string $documentCollection = '',
        public ?string $youtubeCollection = '',
        public ?string $videoCollection = '',
        public ?string $audioCollection = '',
        public ?string $mediaManagerId = '',
    ) {
        parent::__construct($id, $frontendTheme);

        $this->id = $this->id . '-ie-update-form';

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
    }

    public function render(): View
    {
        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

        return $this->getPartialView('image-editor-form', $this->frontendTheme);
    }
}
