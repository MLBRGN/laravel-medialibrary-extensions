<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Exception;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;

class UploadForm extends BaseComponent
{
    public bool $mediaPresent = false;

    public string $allowedMimeTypesHuman = '';

    public HasMedia|null $model = null;

    public ?string $modelType = null;

    public mixed $modelId = null;
    //    public bool $temporaryUpload = false;

    public function __construct(
        public string $id,
        public ?string $frontendTheme,
        public ?string $imageCollection,
        public ?string $documentCollection,
        public ?string $youtubeCollection,
        public ?string $videoCollection,
        public ?string $audioCollection,
        public HasMedia|string $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public bool $temporaryUpload = false,
        public string $allowedMimeTypes = '',
        public bool $multiple = false,
        public bool $destroyEnabled = false,
        public bool $setAsFirstEnabled = false,
        public ?bool $useXhr = null,
    ) {
        parent::__construct($id, $frontendTheme);
    }

    public function render(): View
    {

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

        $this->setAllowedMimeTypes();

        // TODO look at this
        $this->mediaPresent = $this->model && $this->imageCollection
            ? $this->model->hasMedia($this->imageCollection)
            : false;

        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

        return $this->getPartialView('upload-form', $this->frontendTheme);
    }

    private function setAllowedMimeTypes(): void
    {
        // TODO attribute mimetype override?
        $allowedMimeTypes = collect();
        if ($this->imageCollection) {
            $allowedMimeTypes = $allowedMimeTypes->merge(config('media-library-extensions.allowed_mimetypes.image'));
        }
        if ($this->documentCollection) {
            $allowedMimeTypes = $allowedMimeTypes->merge(config('media-library-extensions.allowed_mimetypes.document'));
        }
        if ($this->videoCollection) {
            $allowedMimeTypes = $allowedMimeTypes->merge(config('media-library-extensions.allowed_mimetypes.video'));
        }
        if ($this->audioCollection) {
            $allowedMimeTypes = $allowedMimeTypes->merge(config('media-library-extensions.allowed_mimetypes.audio'));
        }

        $this->allowedMimeTypesHuman = $allowedMimeTypes
            ->map(fn ($mime) => mimetype_label($mime))
            ->join(', ');

        $this->allowedMimeTypes = $allowedMimeTypes
            ->flatten()
            ->unique()
            ->implode(',');

    }
}
