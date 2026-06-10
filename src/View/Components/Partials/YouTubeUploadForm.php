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

    //    public array $config = [];

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or its class name
        public Media|TemporaryUpload|null $singleMedia = null,
        public array $collections = [],
        array $options = [],
        public bool $multiple = false,
        public ?bool $readonly = false,
        public ?bool $disabled = false,
        public string $instanceId = '',
    ) {
        $this->mediaManagerId = $id;

        parent::__construct($id);
        if ($instanceId) {
            $this->instanceId = $instanceId;
        }
        $this->options = $options;

        $this->resolveModelOrClassName($modelOrClassName);

        $youtubeCollection = $collections['youtube'] ?? null;
        $mediaUploadRoute = route(mle_prefix_route('media-upload-youtube'));
        $mediaManagerPreviewUpdateRoute = route(mle_prefix_route('media-manager-preview-update')); // : route(mle_prefix_route('media-upload-single-preview'));

        $this->resolveConfig([
            'instanceId' => $this->instanceId,
            //            'frontendTheme' => config('medialibrary-extensions.frontend_theme'),
            //            'useXhr' => config('medialibrary-extensions.use_xhr'),
            'youtubeCollection' => $youtubeCollection,
            'mediaUploadRoute' => $mediaUploadRoute,
            'mediaManagerPreviewUpdateRoute' => $mediaManagerPreviewUpdateRoute,
        ]);
    }

    public function render(): View
    {
        return $this->renderView('youtube-upload-form', $this->getConfig('frontendTheme'), true);
    }
}
