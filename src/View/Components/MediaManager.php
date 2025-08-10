<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\View\Components;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaManager extends BaseComponent
{
    public array $config;

    public string $allowedMimeTypes = '';

    public HasMedia|null $model = null;

    public ?string $modelType = null;

    public mixed $modelId = null;

    public bool $temporaryUpload = false;

    /** @var Collection<int, Media> */
    public Collection $media;

    public string $mediaUploadRoute; // upload form action route

    public string $previewUpdateRoute; // route to update preview media when using XHR

    public string $youtubeUploadRoute; // route to upload a YouTube video using XHR

    public function __construct(
        public HasMedia|string $modelOrClassName,// either a modal that implements HasMedia or it's class name
        public string $imageCollection = '',
        public string $documentCollection = '',
        public string $youtubeCollection = '',
        public string $videoCollection = '',
        public string $audioCollection = '',
        public bool $uploadEnabled = false,
        public string $uploadFieldName = 'media',
        public bool $destroyEnabled = false,
        public bool $setAsFirstEnabled = false,
        public bool $showMediaUrl = false,
        public bool $showOrder = false,
        public bool $showMenu = true,
        public string $id = '',
        public ?string $frontendTheme = null,
        public ?bool $useXhr = true,
        public bool $multiple = false,
    ) {
        parent::__construct($id, $frontendTheme);

        $this->frontendTheme = $frontendTheme ? $this->frontendTheme : config('medialibrary-extensions.frontend_theme', 'bootstrap-5');

        if ($modelOrClassName instanceof HasMedia) {
            $this->model = $modelOrClassName;
            $this->modelType = $modelOrClassName->getMorphClass();
            $this->modelId = $modelOrClassName->getKey();
        } elseif (is_string($modelOrClassName)) {
            $this->model = null;
            $this->modelType = $modelOrClassName;
            $this->modelId = null;
            $this->temporaryUpload = true;
        } else {
            throw new Exception('model-or-class-name must be either a HasMedia model or a string representing the model class');
        }

        // set allowed mimetypes
        $this->allowedMimeTypes = collect(config('media-library-extensions.allowed_mimes.image'))
            ->flatten()
            ->unique()
            ->implode(',');

        // Always disable "set-as-first" when multiple files disabled
        if (!$this->multiple) {
            $this->setAsFirstEnabled = false;
        }

        $collections = collect();
        if ($this->model) {
            if ($imageCollection) {
                $collections = $collections->merge($this->model->getMedia($imageCollection));
            }

            if ($youtubeCollection) {
                $collections = $collections->merge($this->model->getMedia($youtubeCollection));
            }

            if ($documentCollection) {
                $collections = $collections->merge($this->model->getMedia($documentCollection));
            }
        }
        $this->media = $collections;

        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

        // the routes, "set-as-first" and "destroy" are "medium specific" routes, so not defined here
        $this->previewUpdateRoute = route(mle_prefix_route('preview-update'));
        $this->youtubeUploadRoute = route(mle_prefix_route('media-upload-youtube'));

        if ($this->multiple) {
            $this->uploadFieldName = config('media-library-extensions.upload_field_name_multiple');
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-multiple'));
            $this->id = $this->id.'-media-manager-multiple';
        } else {
            $this->uploadFieldName = config('media-library-extensions.upload_field_name_single');
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-single'));
            $this->id = $this->id.'-media-manager-single';
        }

        // Config array passed to view
        $this->config = [
            'id' => $this->id,
            //            'model' => $this->model,
            'model_type' => $this->modelType,
            'model_id' => $this->modelId,
            'image_collection' => $this->imageCollection,
            'document_collection' => $this->documentCollection,
            'youtube_collection' => $this->youtubeCollection,
            'media_upload_route' => $this->mediaUploadRoute,
            'temporary_upload_route' => 'TODO',
            'preview_update_route' => $this->previewUpdateRoute,
            'temporary_upload_preview_update_route' => 'TODO',
            'youtube_upload_route' => $this->youtubeUploadRoute,
            'csrf_token' => csrf_token(),
            'frontend_theme' => $this->frontendTheme,
            'destroy_enabled' => $this->destroyEnabled,
            'set_as_first_enabled' => $this->setAsFirstEnabled,
            'show_media_url' => $this->showMediaUrl,
            'show_order' => $this->showOrder,
            'temporary_upload' => $this->temporaryUpload ? 'true' : 'false',
        ];
    }

    public function render(): View
    {
        return $this->getView('media-manager', $this->frontendTheme);
    }
}
