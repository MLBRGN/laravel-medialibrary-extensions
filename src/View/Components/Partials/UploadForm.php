<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;

class UploadForm extends BaseComponent
{

    use ResolveModelOrClassName;

    public ?string $mediaManagerId = '';
    public string $allowedMimeTypesHuman = '';

    public function __construct(
        public string $id,
        ?string $frontendTheme,
        public ?string $imageCollection,
        public ?string $documentCollection,
        public ?string $youtubeCollection,
        public ?string $videoCollection,
        public ?string $audioCollection,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public string $allowedMimeTypes = '',
        public bool $multiple = false,
        public bool $destroyEnabled = false,
        public bool $setAsFirstEnabled = false,
        public ?bool $useXhr = null,
        public ?bool $disabled = false,
    ) {
        $this->mediaManagerId = $this->id;

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
        if (!empty($this->allowedMimeTypes)) {
            $this->allowedMimeTypesHuman = collect(explode(',', $this->allowedMimeTypes))
                ->map(fn($mime) => mle_human_mimetype_label($mime))
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
            ->map(fn($mime) => mle_human_mimetype_label($mime))
            ->join(', ');

        $this->allowedMimeTypes = $allowedMimeTypes
            ->flatten()
            ->unique()
            ->implode(',');

    }

}
