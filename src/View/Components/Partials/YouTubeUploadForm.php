<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMimeTypes;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class YouTubeUploadForm extends BaseComponent
{
    use ResolveModelOrClassName;
    use InteractsWithOptionsAndConfig;

    public string $mediaUploadRoute; // upload form action route

    public string $previewUpdateRoute; // route to update preview media when using ajax

    public ?string $modelType = null;

    public ?string $mediaManagerId = '';

    public bool $multiple = false;

    public bool $disabled = false;

    public ?string $youtubeCollection = null;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload|null $medium = null,
        public array $collections = [], // in image, document, youtube, video, audio
        public array $options = [],
    ) {
        $this->mediaManagerId = $id;

        parent::__construct($id, $this->getOption('frontendTheme'));

        $this->resolveModelOrClassName($modelOrClassName);

        $this->youtubeCollection = $collections['youtube'];
        $this->mediaUploadRoute = route(mle_prefix_route('media-upload-youtube'));
        $this->previewUpdateRoute = route(mle_prefix_route('preview-update')); // : route(mle_prefix_route('media-upload-single-preview'));

//        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');
        $this->initializeConfig([
            'frontendTheme' => config('media-library-extensions.frontend_theme'),
            'useXhr' => config('media-library-extensions.use_xhr'),
        ]);
    }

    public function render(): View
    {
        return $this->getPartialView('youtube-upload-form', $this->frontendTheme);
    }
}
