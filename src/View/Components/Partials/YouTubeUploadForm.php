<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class YouTubeUploadForm extends BaseComponent
{
    use ResolveModelOrClassName;

    public string $mediaUploadRoute; // upload form action route

    public string $previewUpdateRoute; // route to update preview media when using ajax

    public ?string $modelType = null;

    public ?string $mediaManagerId = '';

    public bool $multiple = false;

    public bool $disabled = false;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload|null $medium = null,
        public ?string $youtubeCollection,
        ?string $frontendTheme,
        public array $collections = [], // in image, document, youtube, video, audio
        public array $options = [],
        //        public ?string $mediaCollection,// TODO remove?
        //        public string $allowedMimeTypes = '',
        //        public bool $multiple = false,
        //        public bool $showDestroyButton = false,
        //        public bool $showSetAsFirstButton = false,
        public ?bool $useXhr = null,
        //        public ?bool $readonly = false,
        //        public ?bool $disabled = false,
    ) {
        $this->mediaManagerId = $id;

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
