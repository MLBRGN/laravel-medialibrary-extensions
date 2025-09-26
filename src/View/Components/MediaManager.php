<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Mlbrgn\MediaLibraryExtensions\Traits\ResolveModelOrClassName;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManager extends BaseComponent
{
    use ResolveModelOrClassName;

    public array $config;

    public bool $disableForm = false;

    public string $mediaUploadRoute; // upload form action route

    public string $previewUpdateRoute; // route to update preview media when using XHR

    public string $youtubeUploadRoute; // route to upload a YouTube video using XHR

    public function __construct(
        public mixed $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public string $imageCollection = '',
        public string $documentCollection = '',
        public string $youtubeCollection = '',
        public string $videoCollection = '',
        public string $audioCollection = '',
        public bool $showUploadForm = true,
        public string $uploadFieldName = 'media',
        public bool $showDestroyButton = false,
        public bool $showSetAsFirstButton = false,
        public bool $showOrder = false,
        public bool $showMenu = true,
        public string $id = '',
        public ?string $frontendTheme = null,
        public ?bool $useXhr = true,
        public bool $multiple = false,
        public string $allowedMimeTypes = '',
        public bool $selectable = false,
        public bool $showMediaEditButton = false,// (at the moment) only for image editing
        public bool $readonly = false,
        public bool $disabled = false,
    ) {
        parent::__construct($id, $frontendTheme);

        $this->frontendTheme = $frontendTheme ? $this->frontendTheme : config('medialibrary-extensions.frontend_theme', 'bootstrap-5');

        $this->resolveModelOrClassName($modelOrClassName);

        // override: enforce disabled / readonly
//        if ($this->readonly || $this->disabled) {
//            $this->showDestroyButton = false;
//            $this->showSetAsFirstButton = false;
//            $this->selectable = false;
//        }

        // Override: enforce disabling "set-as-first" when multiple is disabled
        if (!$this->multiple) {
            $this->showSetAsFirstButton = false;
        }

        // Override: enforce disabling upload when no uploadable media collections provided, youtube collection not included
        if (!$this->imageCollection && !$this->documentCollection && !$this->videoCollection && !$this->audioCollection) {
//            $this->showUploadForm = false;
        }

        // throw exception when no media collection provided at all
        if (!$this->imageCollection && !$this->documentCollection && !$this->videoCollection && !$this->audioCollection && !$this->youtubeCollection) {
           throw new Exception(__('media-library-extensions::messages.no_media_collections'));
        }

        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

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
        $this->config = [
            'id' => $this->id,
            'model_type' => $this->modelType,
            'model_id' => $this->modelId,
            'image_collection' => $this->imageCollection,
            'document_collection' => $this->documentCollection,
            'video_collection' => $this->videoCollection,
            'audio_collection' => $this->audioCollection,
            'youtube_collection' => $this->youtubeCollection,
            'media_upload_route' => $this->mediaUploadRoute,
            'preview_update_route' => $this->previewUpdateRoute,
            'youtube_upload_route' => $this->youtubeUploadRoute,
            'csrf_token' => csrf_token(),
            'frontend_theme' => $this->frontendTheme,
            'show_upload_form' => $this->showUploadForm,
            'show_destroy_button' => $this->showDestroyButton,
            'show_set_as_first_button' => $this->showSetAsFirstButton,
            'show_order' => $this->showOrder,
            'show_menu' => $this->showMenu,
            'temporary_upload' => $this->temporaryUpload ? 'true' : 'false',
            'multiple' => $this->multiple,
            'use_xhr' => $this->useXhr,
            'selectable' => $this->selectable,
            'show_media_edit_button' => $this->showMediaEditButton,
            'readonly' => $this->readonly,
            'disabled' => $this->disabled,
        ];
    }

    public function render(): View
    {
        return $this->getView('media-manager', $this->frontendTheme);
    }

}
