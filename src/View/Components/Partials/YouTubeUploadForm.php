<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Exception;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;

class YouTubeUploadForm extends BaseComponent
{
    use ResolveModelOrClassName;
    public string $mediaUploadRoute; // upload form action route

    public string $previewUpdateRoute; // route to update preview media when using ajax
    public ?string $modelType = null;
    public ?string $mediaManagerId = '';

    public function __construct(
        public ?string $youtubeCollection,
        public string $id,
        public ?string $frontendTheme,
        public ?string $mediaCollection,// TODO remove?
        public ?string $imageCollection,
        public ?string $documentCollection,
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

        $this->mediaUploadRoute = route(mle_prefix_route('media-upload-youtube'));
        $this->previewUpdateRoute = route(mle_prefix_route('preview-update')); // : route(mle_prefix_route('media-upload-single-preview'));
        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

    }

    public function render(): View
    {
        return $this->getPartialView('youtube-upload-form', $this->frontendTheme);
    }
}
