<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
//use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithCollections;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManager extends BaseComponent
{
//    use InteractsWithCollections;
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    protected string $mediaUploadRoute; // upload form action route

    protected string $previewUpdateRoute; // route to update preview media when using XHR

    protected string $youtubeUploadRoute; // route to upload a YouTube video using XHR

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload|null $medium = null, // when provided, skip collection lookups and just use this medium
        public array $collections = [],
        public array $options = [],
        public bool $multiple = false,
        public bool $disabled = false,
        public bool $readonly = false,
        public bool $selectable = false,
    ) {

        $id = filled($id) ? $id : null;
        parent::__construct($id);

        $this->resolveModelOrClassName($modelOrClassName);

        // Override: enforce disabling "set-as-first" when multiple is disabled
        if (! $this->multiple) {
            $this->setOption('showSetAsFirstButton', false);
        }

        // throw exception when no media collection provided at all
        if (! $this->hasCollections()) {
            throw new Exception(__('media-library-extensions::messages.no_media_collections'));
        }

        // override
        if (! $this->hasCollection('image') && ! $this->hasCollection('document') && ! $this->hasCollection('video') && ! $this->hasCollection('audio')) {
            $this->setOption('showUploadForm', false);
        }

        // override
        if (! $this->hasCollection('youtube')) {
            $this->setOption('showYouTubeUploadForm', false);
        }

        // the routes, "set-as-first" and "destroy" are "medium specific" routes, so not defined here
        $this->previewUpdateRoute = route(mle_prefix_route('preview-update'));
        $this->youtubeUploadRoute = route(mle_prefix_route('media-upload-youtube'));

        if ($this->multiple) {
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-multiple'));
            $this->setOption('uploadFieldName', config('media-library-extensions.upload_field_name_multiple'));
            $this->id .= '-mmm';
        } else {
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-single'));
            $this->setOption('uploadFieldName', config('media-library-extensions.upload_field_name_single'));
            $this->id .= '-mms';
        }

        $this->initializeConfig();

        // TODO is there a neater way to do this?
        // options are passed to components, config is reinitialized for each component.
        // override hide media menu when nothing to see inside menu
        // since i use config have to do this after config has been initialized
        if (
            $this->getConfig('showDestroyButton') === false &&
            $this->getConfig('showSetAsFirstButton') === false &&
            $this->getConfig('showMediaEditButton') === false

        ) {
            $this->options['showMenu']  = false;
            $this->config['showMenu']  = false;
        }
    }

    public function render(): View
    {
        return $this->getView('media-manager', $this->getConfig('frontendTheme'));
    }
}
