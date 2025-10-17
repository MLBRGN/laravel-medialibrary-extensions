<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Models\TemporaryUpload;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithCollections;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManager extends BaseComponent
{
    use ResolveModelOrClassName;
    use InteractsWithOptionsAndConfig;
    use InteractsWithCollections;

    protected string $mediaUploadRoute; // upload form action route

    protected string $previewUpdateRoute; // route to update preview media when using XHR

    protected string $youtubeUploadRoute; // route to upload a YouTube video using XHR

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public Media|TemporaryUpload|null $medium = null, // when provided, skip collection lookups and just use this medium
        public array $collections = [], // in image, document, youtube, video, audio
        public array $options = [],
        public bool $multiple = false,
        public bool $disabled = false,
        public bool $readonly = false,
        public bool $selectable = false,
    ) {

        $id = filled($id) ? $id : null;
        $frontendTheme = $this->options['frontendTheme'] ?? config('media-library-extensions.frontend_theme', 'bootstrap-5');
        $this->frontendTheme = $frontendTheme;

        parent::__construct($id, $frontendTheme);

        $collections = $this->mergeCollections($collections);

        $this->resolveModelOrClassName($modelOrClassName);

        // Override: enforce disabling "set-as-first" when multiple is disabled
        if (! $this->multiple) {
            $this->setOption('showSetAsFirstButton', false);
        }

        // throw exception when no media collection provided at all
        if (! $this->hasCollections()) {
            throw new Exception(__('media-library-extensions::messages.no_media_collections'));
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

        // merge into config
        $this->initializeConfig([
            'frontendTheme' => $this->frontendTheme,
            'useXhr' => $this->options['useXhr'] ?? config('media-library-extensions.use_xhr', true),
//            'csrfToken' => csrf_token(),
        ]);
    }

    public function render(): View
    {
        return $this->getView('media-manager', $this->frontendTheme);
    }

}
