<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components\Partials;

use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Support\InstanceManager;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\View\Components\BaseMediaComponent;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

// TODO $dataSource?
class YouTubeUploadForm extends BaseMediaComponent
{
    use InteractsWithOptionsAndConfig;

    public ?string $modelType = null;

    public ?string $mediaManagerId = '';

    public function __construct(
        ?string $id,
        ?string $mediaManagerId,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or its class name
        public Media|TemporaryUpload|null $singleMedia = null,
        public array $collections = [],
        array $options = [],
        public bool $multiple = false,
        public ?bool $readonly = false,
        public ?bool $disabled = false,
        public string $instanceId = '',
    ) {
        parent::__construct($id);

        $this->mediaManagerId = $mediaManagerId ?? $this->originalId;

        // Ensure instanceId is derived from the mediaManagerId (the parent manager's identity)
        $this->instanceId = InstanceManager::getInstanceId($this->mediaManagerId);

        $this->options = $options;

        $resolvedModel = $this->mediaService->resolveModelOrClassName($modelOrClassName, 'default');// TODO use default?
        $this->setModelProperties($resolvedModel);

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
