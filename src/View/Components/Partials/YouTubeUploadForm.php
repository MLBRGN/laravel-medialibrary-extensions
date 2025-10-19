<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseComponent;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class YouTubeUploadForm extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public ?string $modelType = null;

    public ?string $mediaManagerId = '';

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or its class name
        public Media|TemporaryUpload|null $medium = null,
        public array $collections = [],
        public array $options = [],
        public bool $multiple = false,
        public ?bool $readonly = false,
        public ?bool $disabled = false,
    ) {
        $this->mediaManagerId = $id;

        parent::__construct($id, $this->getOption('frontendTheme'));

        $this->resolveModelOrClassName($modelOrClassName);

        $youtubeCollection = $collections['youtube'];
        $mediaUploadRoute = route(mle_prefix_route('media-upload-youtube'));
        $previewUpdateRoute = route(mle_prefix_route('preview-update')); // : route(mle_prefix_route('media-upload-single-preview'));

        $this->initializeConfig([
//            'frontendTheme' => config('media-library-extensions.frontend_theme'),
//            'useXhr' => config('media-library-extensions.use_xhr'),
            'youtubeCollection' => $youtubeCollection,
            'mediaUploadRoute' => $mediaUploadRoute,
            'previewUpdateRoute' => $previewUpdateRoute,
        ]);
    }

    public function render(): View
    {
        return $this->getPartialView('youtube-upload-form', $this->getConfig('frontendTheme'));
    }
}
