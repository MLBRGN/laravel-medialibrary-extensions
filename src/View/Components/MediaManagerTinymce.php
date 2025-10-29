<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithOptionsAndConfig;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;

class MediaManagerTinymce extends BaseComponent
{
    use InteractsWithOptionsAndConfig;
    use ResolveModelOrClassName;

    public array $config;

    public bool $disableForm = false;

    // TODO not used?
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Foundation\Application|mixed|object|null
     */
    public string $uploadFieldName;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public array $collections = [],
        public array $options = [],
        public bool $multiple = false,
        public bool $disabled = false,
        public bool $readonly = false,
        public bool $selectable = false,
    ) {

//        dd([
//            'disabled' => $disabled,
//            'readonly' => $readonly,
//            'selectable' => $selectable,
//        ]);

        $id = filled($id) ? $id : null;
        parent::__construct($id);

        $this->resolveModelOrClassName($modelOrClassName);

        // override: enforce disabled / readonly
        if ($this->readonly || $this->disabled) {
            dump('disable readonly or disabled');
            $this->setOption('showUploadForm', false);
            $this->setOption('showDestroyButton', false);
            $this->setOption('showSetAsFirstButton', false);
        }

        // throw exception when no media collection provided at all
        if (! $this->hasCollections()) {
            throw new Exception(__('media-library-extensions::messages.no_media_collections'));
        }

        // override
        if (! $this->hasCollection('image') && ! $this->hasCollection('document') && ! $this->hasCollection('video') && ! $this->hasCollection('audio')) {
            dump('disable');
            $this->setOption('showUploadForm', false);
        }

        // override
        if (! $this->hasCollection('youtube')) {
            $this->setOption('showYouTubeUploadForm', false);
        }

        // Override: Always disable "set-as-first" when multiple files disabled
        if (! $this->multiple) {
            $this->setOption('showSetAsFirstButton', false);
        }

        // the routes, "set-as-first" and "destroy" are "medium specific" routes, so not defined here
        $mediaManagerPreviewUpdateRoute = route(mle_prefix_route('media-manager-preview-update'));
        $youtubeUploadRoute = route(mle_prefix_route('media-upload-youtube'));

        if ($this->multiple) {
            $mediaUploadRoute = route(mle_prefix_route('media-upload-multiple'));
            $this->uploadFieldName = config('media-library-extensions.upload_field_name_multiple');
            $this->id = $this->id.'-mmm';
        } else {
            $mediaUploadRoute = route(mle_prefix_route('media-upload-single'));
            $this->uploadFieldName = config('media-library-extensions.upload_field_name_single');
            $this->id = $this->id.'-mms';
        }

        $this->initializeConfig([
            'mediaManagerPreviewUpdateRoute' => $mediaManagerPreviewUpdateRoute,
            'youtubeUploadRoute' => $youtubeUploadRoute,
            'mediaUploadRoute' => $mediaUploadRoute,
            'uploadFieldName' => $this->uploadFieldName,
            'selectable' => $selectable,
        ]);
    }

    public function render(): View
    {
        return $this->getView('media-manager-tinymce', $this->getConfig('frontendTheme'));
    }
}
