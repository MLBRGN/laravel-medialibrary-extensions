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

    public string $mediaUploadRoute; // upload form action route

    public string $previewUpdateRoute; // route to update preview media when using XHR

    public string $youtubeUploadRoute; // route to upload a YouTube video using XHR

    //    protected array $optionKeys = [
    //        'allowedMimeTypes',
    //        'disabled',
    //        'readonly',
    //        'selectable',
    //        'frontendTheme',
    //        'showDestroyButton',
    //        'showMediaEditButton',
    //        'showMenu',
    //        'showOrder',
    //        'showSetAsFirstButton',
    //        'showUploadForm',
    //        'temporaryUploadMode',
    //        'uploadFieldName',
    //        'useXhr',
    //        //        'frontendTheme',
    //    ];

    // TODO not used?
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Foundation\Application|mixed|object|null
     */
    public string $uploadFieldName;

    public function __construct(
        ?string $id,
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        array $collections = [],
        public array $options = [],
        public bool $multiple = false,
        public bool $readonly = false,
        public bool $disabled = false
    ) {

        parent::__construct($id);

        $this->resolveModelOrClassName($modelOrClassName);

        // override: enforce disabled / readonly
        if ($this->readonly || $this->disabled) {
            $this->setOption('showUploadForm', false);
            $this->setOption('showDestroyButton', false);
            $this->setOption('showSetAsFirstButton', false);
        }

        // throw exception when no media collection provided at all
        if (! $this->hasCollections()) {
            throw new Exception(__('media-library-extensions::messages.no_media_collections'));
        }

        // Override: Always disable "set-as-first" when multiple files disabled
        if (! $this->multiple) {
            $this->setOption('showSetAsFirstButton', false);
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
            $this->uploadFieldName = config('media-library-extensions.upload_field_name_multiple');
            $this->id = $this->id.'-mmm';
        } else {
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-single'));
            $this->uploadFieldName = config('media-library-extensions.upload_field_name_single');
            $this->id = $this->id.'-mms';
        }

        // Config array passed to view
        //        $this->config = [
        //            'id' => $this->id,
        //            'modelType' => $this->modelType,
        //            'modelId' => $this->modelId,
        //            'collections' => $collections,
        //            'mediaUploadRoute' => $this->mediaUploadRoute,
        //            'previewUpdateRoute' => $this->previewUpdateRoute,
        //            'youtubeUploadRoute' => $this->youtubeUploadRoute,
        //            'csrfToken' => csrf_token(),
        //            'options' => $this->options,
        //            'multiple' => $this->multiple,
        //            'readonly' => $this->readonly,
        //            'disabled' => $this->disabled,
        // //            'frontendTheme' => $this->frontendTheme,
        // //            'showDestroyButton' => $this->showDestroyButton,
        // //            'showSetAsFirstButton' => $this->showSetAsFirstButton,
        // //            'showOrder' => $this->showOrder,
        // //            'showMenu' => $this->showMenu,
        // //            'temporaryUpload' => $this->temporaryUpload ? 'true' : 'false',
        // //            'useXhr' => $this->useXhr,
        // //            'showMediaEditButton' => $this->showMediaEditButton,
        //        ];

        $this->initializeConfig();
    }

    public function render(): View
    {
        return $this->getView('media-manager-tinymce', $this->getConfig('frontendTheme'));
    }
}
