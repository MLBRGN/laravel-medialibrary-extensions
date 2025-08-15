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

    public bool $disableForm = false;

    /** @var Collection<int, Media> */
//    public Collection $media;

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

//        $this->setAllowedMimeTypes();

        // Override: Always disable "set-as-first" when multiple files disabled
        if (!$this->multiple) {
            $this->setAsFirstEnabled = false;
        }

        // Override: Always set upload enabled to false when no document collections provided
        if (!$this->imageCollection && !$this->documentCollection && !$this->videoCollection && !$this->audioCollection) {
            $this->uploadEnabled = false;
        }

        if (!$this->imageCollection && !$this->documentCollection && !$this->videoCollection && !$this->audioCollection && !$this->youtubeCollection) {
           throw new Exception(__('media-library-extensions::messages.no_media_collections'));
        }

        // TODO GETTING MEDIA SHOULD NOT BE NECESSARY HERE
//        $collections = collect();
//        if ($this->model) {
//            $collections = collect([
//                $imageCollection,
//                $youtubeCollection,
//                $documentCollection,
//                $videoCollection,
//                $audioCollection,
//            ])
//                ->filter()// remove falsy values
//                ->reduce(
//                    fn($carry, $name) => $carry->merge($this->model->getMedia($name)),
//                    collect()
//                );
//        }
//
//        // Sort by custom property "priority"
//        $this->media = $collections
//            ->sortBy(fn(Media $m) => $m->getCustomProperty('priority', PHP_INT_MAX))
//            ->values();

        // Temporarily inspect what the view will get
//         dump($this->media->map(fn ($m) => [
//             'id' => $m->id,
//             'priority' => $m->getCustomProperty('priority'),
//             'file_name' => $m->file_name,
//             'order' => $m->order_column,
//             'collection' => $m->collection_name,
//         ]));
//        dump($this->media);

        $this->useXhr = ! is_null($this->useXhr) ? $this->useXhr : config('media-library-extensions.use_xhr');

        // the routes, "set-as-first" and "destroy" are "medium specific" routes, so not defined here
        $this->previewUpdateRoute = route(mle_prefix_route('preview-update'));
        $this->youtubeUploadRoute = route(mle_prefix_route('media-upload-youtube'));

        if ($this->multiple) {
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-multiple'));
            $this->uploadFieldName = config('media-library-extensions.upload_field_name_multiple');
            $this->id = $this->id.'-media-manager-multiple';
        } else {
            $this->mediaUploadRoute = route(mle_prefix_route('media-upload-single'));
            $this->uploadFieldName = config('media-library-extensions.upload_field_name_single');
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
            'video_collection' => $this->videoCollection,
            'audio_collection' => $this->audioCollection,
            'youtube_collection' => $this->youtubeCollection,
            'media_upload_route' => $this->mediaUploadRoute,
            'preview_update_route' => $this->previewUpdateRoute,
            'youtube_upload_route' => $this->youtubeUploadRoute,
            'csrf_token' => csrf_token(),
            'frontend_theme' => $this->frontendTheme,
            'destroy_enabled' => $this->destroyEnabled,
            'set_as_first_enabled' => $this->setAsFirstEnabled,
            'show_media_url' => $this->showMediaUrl,
            'show_order' => $this->showOrder,
            'temporary_upload' => $this->temporaryUpload ? 'true' : 'false',
            'multiple' => $this->multiple,
        ];
    }

    public function render(): View
    {
        return $this->getView('media-manager', $this->frontendTheme);
    }

//    private function setAllowedMimeTypes(): void
//    {
//        $allowedMimeTypes = collect();
//        if ($this->imageCollection) {
//            $allowedMimeTypes = $allowedMimeTypes->merge(config('media-library-extensions.allowed_mimetypes.image'));
//        }
//        if ($this->documentCollection) {
//            $allowedMimeTypes = $allowedMimeTypes->merge(config('media-library-extensions.allowed_mimetypes.document'));
//        }
//        if ($this->videoCollection) {
//            $allowedMimeTypes = $allowedMimeTypes->merge(config('media-library-extensions.allowed_mimetypes.video'));
//        }
//        if ($this->audioCollection) {
//            $allowedMimeTypes = $allowedMimeTypes->merge(config('media-library-extensions.allowed_mimetypes.audio'));
//        }
//
//        $this->allowedMimeTypes = $allowedMimeTypes
//            ->flatten()
//            ->unique()
//            ->implode(',');
//
//    }
}
