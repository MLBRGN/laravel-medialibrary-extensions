<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UploadForm extends BaseComponent
{
    use ResolveModelOrClassName;

    public ?string $mediaManagerId = '';

    public string $allowedMimeTypesHuman = '';
    //    public ?string $imageCollection;
    //    public ?string $documentCollection;
    //    public ?string $youtubeCollection;
    //    public ?string $videoCollection;
    //    public ?string $audioCollection;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload|null $medium = null,
        ?string $frontendTheme,// TODO scope?
        public array $collections = [], // in image, document, youtube, video, audio
        public array $options = [],
        public string $allowedMimeTypes = '',
        public bool $multiple = false,
        public bool $showDestroyButton = false,
        public bool $showSetAsFirstButton = false,
        public ?bool $useXhr = null,
        public ?bool $readonly = false,
        public ?bool $disabled = false,
    ) {
        $this->mediaManagerId = $id;

        // define default collection names
        $collections = array_merge([
            'image' => '',
            'document' => '',
            'youtube' => '',
            'video' => '',
            'audio' => '',
        ], $collections);

        $this->imageCollection = $collections['image'];
        $this->audioCollection = $collections['audio'];
        $this->videoCollection = $collections['video'];
        $this->documentCollection = $collections['document'];
        $this->youtubeCollection = $collections['youtube'];

        parent::__construct($id, $frontendTheme);

        $this->resolveModelOrClassName($modelOrClassName);
        $this->setAllowedMimeTypes();

        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');
    }

    public function render(): View
    {
        return $this->getPartialView('upload-form', $this->frontendTheme);
    }

    private function setAllowedMimeTypes(): void
    {
        // Use override if provided
        if (! empty($this->allowedMimeTypes)) {
            $this->allowedMimeTypesHuman = collect(explode(',', $this->allowedMimeTypes))
                ->map(fn ($mime) => mle_human_mimetype_label($mime))
                ->join(', ');

            return;
        }

        // Allowed mimetypes based on provided collections
        $allowedMimeTypes = collect();

        if ($this->imageCollection) {
            $allowedMimeTypes = $allowedMimeTypes->merge(config('media-library-extensions.allowed_mimetypes.image', []));
        }

        if ($this->documentCollection) {
            $allowedMimeTypes = $allowedMimeTypes->merge(config('media-library-extensions.allowed_mimetypes.document', []));
        }

        if ($this->videoCollection) {
            $allowedMimeTypes = $allowedMimeTypes->merge(config('media-library-extensions.allowed_mimetypes.video', []));
        }

        if ($this->audioCollection) {
            $allowedMimeTypes = $allowedMimeTypes->merge(config('media-library-extensions.allowed_mimetypes.audio', []));
        }

        $allowedMimeTypes = $allowedMimeTypes->flatten()->unique();

        $this->allowedMimeTypesHuman = $allowedMimeTypes
            ->map(fn ($mime) => mle_human_mimetype_label($mime))
            ->join(', ');

        $this->allowedMimeTypes = $allowedMimeTypes
            ->flatten()
            ->unique()
            ->implode(',');

    }
}
