<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Exception;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;

class YouTubeUploadForm extends BaseComponent
{
    public string $mediaUploadRoute; // upload form action route

    public string $previewUpdateRoute; // route to update preview media when using ajax

    public HasMedia|null $model = null;

    public ?string $modelType = null;

    public mixed $modelId = null;

    public function __construct(
        public ?string $youtubeCollection,
        public string $id,
        public ?string $frontendTheme,
        public ?string $mediaCollection,// TODO remove?
        public ?string $imageCollection,
        public ?string $documentCollection,
        public ?string $videoCollection,
        public ?string $audioCollection,
        public HasMedia|string $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public bool $temporaryUpload = false,
        public string $allowedMimeTypes = '',
        public bool $multiple = false,
        public bool $destroyEnabled = false,
        public bool $setAsFirstEnabled = false,
        public ?bool $useXhr = null,
        public ?bool $disabled = false,
    ) {
        parent::__construct($id, $frontendTheme);

        if ($this->modelOrClassName instanceof HasMedia) {
            $this->model = $this->modelOrClassName;
            $this->modelType = $this->modelOrClassName->getMorphClass();
            $this->modelId = $this->modelOrClassName->getKey();
        } elseif (is_string($this->modelOrClassName)) {
            if (! class_exists($this->modelOrClassName)) {
                throw new Exception(__('media-library-extensions::messages.class_not_found', [
                    'class' => $this->modelOrClassName,
                ]));
            }
            if (! is_subclass_of($this->modelOrClassName, HasMedia::class)) {
                throw new Exception(__('media-library-extensions::messages.must_implement_has_media', [
                    'class' => $this->modelOrClassName,
                    'interface' => HasMedia::class,
                ]));
            }
            $this->model = null;
            $this->modelType = $this->modelOrClassName;
            $this->modelId = null;
        } else {
            throw new Exception('model-or-class-name must be either a HasMedia model or a string representing the model class');
        }

        $this->mediaUploadRoute = route(mle_prefix_route('media-upload-youtube'));
        $this->previewUpdateRoute = route(mle_prefix_route('preview-update')); // : route(mle_prefix_route('media-upload-single-preview'));
        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

    }

    public function render(): View
    {
        return $this->getPartialView('youtube-upload-form', $this->frontendTheme);
    }
}
